<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\Category;
use App\Models\Product;

final class ProductController extends Controller
{
    public function index(): string
    {
        return $this->view('admin/products/index', [
            'title'      => 'Ürünler',
            'products'   => Product::all(),
            'categories' => $this->categoryMap(),
        ]);
    }

    public function create(): string
    {
        return $this->view('admin/products/form', [
            'title'      => 'Yeni Ürün',
            'product'    => null,
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request): void
    {
        $this->verifyCsrf($request);
        $data = $this->validated($request);
        if ($data === null) {
            $this->back();
        }
        Product::create($data);
        Flash::success('Ürün oluşturuldu.');
        redirect('yonetim/urunler');
    }

    public function edit(Request $request, string $id): string
    {
        $product = Product::find((int) $id);
        if ($product === null) {
            return $this->notFound();
        }
        return $this->view('admin/products/form', [
            'title'      => 'Ürünü Düzenle',
            'product'    => $product,
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        $product = Product::find((int) $id);
        if ($product === null) {
            Flash::error('Ürün bulunamadı.');
            redirect('yonetim/urunler');
        }
        $data = $this->validated($request);
        if ($data === null) {
            $this->back();
        }
        Product::update($product->id, $data);
        Flash::success('Ürün güncellendi.');
        redirect('yonetim/urunler');
    }

    public function destroy(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        Product::delete((int) $id);
        Flash::success('Ürün silindi.');
        redirect('yonetim/urunler');
    }

    // ---------------------------------------------------------- categories

    public function categories(): string
    {
        return $this->view('admin/categories', [
            'title'      => 'Kategoriler',
            'categories' => Category::all(),
        ]);
    }

    public function storeCategory(Request $request): void
    {
        $this->verifyCsrf($request);
        $name = $request->string('name');
        if ($name === '') {
            Flash::error('Kategori adı gerekli.');
            $this->back();
        }
        Category::create([
            'name'       => $name,
            'slug'       => slugify($request->string('slug') ?: $name),
            'description'=> $request->string('description'),
            'icon'       => $request->string('icon'),
            'sort_order' => $request->int('sort_order'),
        ]);
        Flash::success('Kategori eklendi.');
        redirect('yonetim/kategoriler');
    }

    public function destroyCategory(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        Category::delete((int) $id);
        Flash::success('Kategori silindi.');
        redirect('yonetim/kategoriler');
    }

    /** @return array<int,string> */
    private function categoryMap(): array
    {
        $map = [];
        foreach (Category::all() as $c) {
            $map[$c->id] = $c->name;
        }
        return $map;
    }

    /** @return array<string,mixed>|null */
    private function validated(Request $request): ?array
    {
        $name = $request->string('name');
        if ($name === '' || $request->float('price') < 0) {
            Flash::error('Ürün adı ve geçerli fiyat zorunludur.');
            return null;
        }
        return [
            'category_id' => $request->int('category_id'),
            'name'        => $name,
            'slug'        => slugify($request->string('slug') ?: $name),
            'description' => $request->string('description'),
            'image'       => $request->string('image') ?: null,
            'price'       => $request->float('price'),
            'sale_price'  => $request->float('sale_price'),
            'commands'    => $request->string('commands'),
            'stock'       => $request->int('stock', -1),
            'is_active'   => $request->bool('is_active') ? 1 : 0,
            'featured'    => $request->bool('featured') ? 1 : 0,
            'sort_order'  => $request->int('sort_order'),
        ];
    }
}
