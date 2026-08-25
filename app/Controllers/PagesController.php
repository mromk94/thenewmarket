<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Response;
use App\Models\Page;

class PagesController
{
    private function render(string $slug): string
    {
        $page = Page::findBySlug($slug);
        if (!$page) {
            throw new HttpException('Page not found.', 404);
        }
        return Response::view('pages/show', [
            'page' => $page,
            'title' => $page['title'],
            'description' => $page['meta_description'] ?? '',
        ]);
    }

    public function about(): string
    {
        return $this->render('about');
    }

    public function contact(): string
    {
        return $this->render('contact');
    }

    public function terms(): string
    {
        return $this->render('terms');
    }

    public function privacy(): string
    {
        return $this->render('privacy');
    }
}
