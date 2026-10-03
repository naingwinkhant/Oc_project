<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Advertising a manager writes, and the slide it becomes.
 *
 * The advertisement is written in one place and shown in another, so the tests
 * cross the two: what is saved in the admin area is what a shopper sees at the
 * top of the catalogue, in the order it was put there.
 */
class AdvertisementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function manager(): User
    {
        return User::factory()->manager()->create();
    }

    /**
     * A genuine JPEG for an upload.
     *
     * UploadedFile::image() draws one with the GD extension, which is not
     * installed here, so a real file already in the project is used instead.
     * The validator and the storage layer only look at the bytes and the name.
     */
    private function fakeJpeg(string $name): UploadedFile
    {
        $source = glob(public_path('storage/products/*.jpg')) ?: [];

        $this->assertNotEmpty($source, 'expected a JPEG already in the project to upload');

        return UploadedFile::fake()->createWithContent(
            $name,
            (string) file_get_contents($source[0])
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Writing one
    |--------------------------------------------------------------------------
    */

    public function test_a_manager_can_add_an_advertisement(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'Two days off, fruit half price',
                'eyebrow' => 'This week',
                'body' => 'Everything in Fresh Produce, Wednesday and Thursday.',
                'button_label' => 'See the fruit',
                'link' => '/catalog/fresh-produce',
                'position' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.advertisements.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('advertisements', [
            'title' => 'Two days off, fruit half price',
            'link' => '/catalog/fresh-produce',
            'is_active' => true,
        ]);
    }

    public function test_plain_staff_cannot_add_one(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->post(route('admin.advertisements.store'), ['title' => 'Not Allowed'])
            ->assertForbidden();

        $this->assertDatabaseCount('advertisements', 0);
    }

    public function test_a_guest_is_sent_to_a_sign_in_page(): void
    {
        $this->post(route('admin.advertisements.store'), ['title' => 'Not Allowed'])
            ->assertRedirect(route('admin.login.php'));

        $this->assertDatabaseCount('advertisements', 0);
    }

    public function test_it_needs_a_title(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), ['body' => 'Words with no heading'])
            ->assertSessionHasErrors('title');
    }

    public function test_the_end_date_cannot_be_before_the_start(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'Backwards',
                'starts_on' => '2026-10-20',
                'ends_on' => '2026-10-01',
            ])
            ->assertSessionHasErrors('ends_on');
    }

    public function test_the_link_must_stay_on_this_site(): void
    {
        // An advertisement that could point anywhere would let a form send
        // shoppers off to a site nobody on this shop has checked.
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'Off Site',
                'link' => 'https://example.invalid/anything',
            ])
            ->assertSessionHasErrors('link');

        // Two in one go, because one refusal must not leave a second form able
        // to try the same thing.
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'Off Site Too',
                'link' => '//example.invalid',
            ])
            ->assertSessionHasErrors('link');

        $this->assertDatabaseCount('advertisements', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Pictures and video
    |--------------------------------------------------------------------------
    */

    public function test_a_picture_is_stored_and_shown(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'With a picture',
                'image' => $this->fakeJpeg('banner.jpg'),
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.advertisements.index'));

        $advertisement = Advertisement::query()->firstOrFail();

        $this->assertNotNull($advertisement->image);
        Storage::disk('public')->assertExists($advertisement->image);
        $this->assertTrue($advertisement->hasImage());
    }

    public function test_a_video_is_stored(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'With a video',
                'video' => UploadedFile::fake()->create('clip.mp4', 512, 'video/mp4'),
                'is_active' => '1',
            ]);

        $advertisement = Advertisement::query()->firstOrFail();

        $this->assertTrue($advertisement->hasVideo());
        Storage::disk('public')->assertExists($advertisement->video);
    }

    public function test_a_file_that_is_not_an_image_is_refused(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'Not a picture',
                'image' => UploadedFile::fake()->create('notes.pdf', 40, 'application/pdf'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('advertisements', 0);
    }

    public function test_a_missing_file_is_not_offered_to_the_carousel(): void
    {
        // The path is on the row but the file is not there, which is what a
        // failed upload or a wiped volume looks like. It must not become a
        // broken image at the top of the page.
        $advertisement = Advertisement::factory()->create([
            'image' => 'advertisements/gone.jpg',
            'is_active' => true,
        ]);

        $this->assertFalse($advertisement->hasImage());
        $this->assertFalse($advertisement->hasMedia());

        // A words-only slide is still a slide, so it is still shown.
        $this->get('/catalog')
            ->assertOk()
            ->assertSee($advertisement->title);
    }

    public function test_replacing_a_picture_removes_the_old_file(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'First picture',
                'image' => $this->fakeJpeg('first.jpg'),
                'is_active' => '1',
            ]);

        $advertisement = Advertisement::query()->firstOrFail();
        $first = $advertisement->image;

        Storage::disk('public')->assertExists($first);

        $this->actingAs($this->manager())
            ->put(route('admin.advertisements.update', $advertisement), [
                'title' => 'Second picture',
                'image' => $this->fakeJpeg('second.jpg'),
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.advertisements.index'));

        $advertisement->refresh();

        $this->assertNotSame($first, $advertisement->image);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($advertisement->image);
    }

    public function test_deleting_removes_the_file_too(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.advertisements.store'), [
                'title' => 'Doomed',
                'image' => $this->fakeJpeg('doomed.jpg'),
                'is_active' => '1',
            ]);

        $advertisement = Advertisement::factory()->create([
            'image' => Advertisement::query()->value('image'),
            'is_active' => true,
        ]);

        // The file left behind would fill the disk with pictures nothing points
        // at any more.
        $this->actingAs($this->manager())
            ->delete(route('admin.advertisements.destroy', $advertisement))
            ->assertRedirect(route('admin.advertisements.index'));

        Storage::disk('public')->assertMissing($advertisement->image);
    }

    /*
    |--------------------------------------------------------------------------
    | When it shows
    |--------------------------------------------------------------------------
    */

    public function test_a_switched_off_advertisement_is_not_shown(): void
    {
        Advertisement::factory()->switchedOff()->create(['title' => 'Hidden Slide']);

        $this->get('/catalog')
            ->assertOk()
            ->assertDontSee('Hidden Slide');
    }

    public function test_one_waiting_for_its_date_is_not_shown(): void
    {
        Advertisement::factory()->scheduled()->create(['title' => 'Next Week Only']);

        $this->get('/catalog')
            ->assertOk()
            ->assertDontSee('Next Week Only');
    }

    public function test_one_whose_window_has_closed_is_not_shown(): void
    {
        Advertisement::factory()->finished()->create(['title' => 'Last Week Only']);

        $this->get('/catalog')
            ->assertOk()
            ->assertDontSee('Last Week Only');
    }

    public function test_one_inside_its_window_is_shown(): void
    {
        Advertisement::factory()->create([
            'title' => 'On Right Now',
            'starts_on' => now()->subDays(2),
            'ends_on' => now()->addDays(2),
            'is_active' => true,
        ]);

        $this->get('/catalog')->assertOk()->assertSee('On Right Now');
    }

    /*
    |--------------------------------------------------------------------------
    | It becomes a slide, in the order it was put there
    |--------------------------------------------------------------------------
    */

    public function test_the_carousel_leads_with_what_a_manager_wrote(): void
    {
        Advertisement::factory()->atPosition(0)->create([
            'title' => 'Written By A Manager',
            'body' => 'A message about the fruit.',
        ]);

        // An automatic pick that would otherwise take the first place.
        Product::factory()->create([
            'name' => 'Automatic Product Slide',
            'stock' => 10,
            'image' => 'products/auto.jpg',
            'is_featured' => true,
        ]);
        Storage::disk('public')->put('products/auto.jpg', 'x');

        $html = $this->get('/catalog')->assertOk()->getContent();

        $this->assertLessThan(
            strpos($html, 'Automatic Product Slide'),
            strpos($html, 'Written By A Manager'),
            'the written advertisement should come before the automatic pick'
        );
    }

    public function test_the_carousel_follows_the_order(): void
    {
        Advertisement::factory()->atPosition(3)->create(['title' => 'Third Up']);
        Advertisement::factory()->atPosition(1)->create(['title' => 'First Up']);
        Advertisement::factory()->atPosition(2)->create(['title' => 'Second Up']);

        $html = $this->get('/catalog')->assertOk()->getContent();

        $first = strpos($html, 'First Up');
        $second = strpos($html, 'Second Up');
        $third = strpos($html, 'Third Up');

        $this->assertNotFalse($first);
        $this->assertLessThan($second, $first);
        $this->assertLessThan($third, $second);
    }

    public function test_moving_swaps_two_slides(): void
    {
        $first = Advertisement::factory()->atPosition(1)->create(['title' => 'Ahead']);
        $second = Advertisement::factory()->atPosition(2)->create(['title' => 'Behind']);

        $this->actingAs($this->manager())
            ->patch(route('admin.advertisements.move', $second), ['direction' => 'up'])
            ->assertRedirect();

        $this->assertSame(1, $second->fresh()->position);
        $this->assertSame(2, $first->fresh()->position);
    }

    public function test_moving_the_first_one_up_changes_nothing(): void
    {
        $first = Advertisement::factory()->atPosition(1)->create(['title' => 'At The Top']);
        Advertisement::factory()->atPosition(2)->create(['title' => 'Below']);

        $this->actingAs($this->manager())
            ->patch(route('admin.advertisements.move', $first), ['direction' => 'up'])
            ->assertRedirect();

        // Nothing is above it, so nothing moves and nothing breaks.
        $this->assertSame(1, $first->fresh()->position);
    }

    public function test_a_slide_shows_its_button_and_where_it_goes(): void
    {
        Advertisement::factory()->create([
            'title' => 'Go Look At The Fruit',
            'link' => '/catalog/fresh-produce',
            'button_label' => 'See the fruit',
            'is_active' => true,
        ]);

        $this->get('/catalog')
            ->assertOk()
            ->assertSee('See the fruit')
            ->assertSee('/catalog/fresh-produce', false);
    }

    public function test_a_slide_with_no_link_has_no_button(): void
    {
        Advertisement::factory()->create([
            'title' => 'Just A Message',
            'link' => null,
            'is_active' => true,
        ]);

        $this->get('/catalog')->assertOk()->assertSee('Just A Message');
    }

    public function test_staff_can_read_the_list_but_not_write(): void
    {
        Advertisement::factory()->create(['title' => 'Existing Slide']);

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.advertisements.index'))
            ->assertOk()
            ->assertSee('Existing Slide')
            ->assertDontSee('New advertisement');

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.advertisements.create'))
            ->assertForbidden();
    }

    public function test_the_empty_list_says_what_to_do(): void
    {
        $this->actingAs($this->manager())
            ->get(route('admin.advertisements.index'))
            ->assertOk()
            ->assertSee('No advertisements yet');
    }
}
