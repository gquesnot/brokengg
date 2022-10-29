<?php

namespace App\Traits;

use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\WithPagination;

trait PaginateTrait
{
    use WithPagination;

    public $page = 1;

    public $perPage = 20;

    public function paginate($collection)
    {
        $offset = max(0, ($this->page - 1) * $this->perPage);
        $items = $collection->slice($offset, $this->perPage);

        return $this->paginator($items, $collection->count(), $this->perPage, $this->page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }

    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function resolvePage()
    {
        return $this->page;
    }
}
