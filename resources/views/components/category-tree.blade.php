@props(['nodes' => []])

<ul class="space-y-0.5">
    @foreach ($nodes as $node)
        <x-category-tree-node :node="$node" />
    @endforeach
</ul>
