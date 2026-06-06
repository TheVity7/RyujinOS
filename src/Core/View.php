<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Plain-PHP view renderer with layout support.
 *
 * Views set their parent layout via $this->layout('name') and content
 * sections through section()/endSection(). The rendered child output is
 * exposed to the layout as $content.
 */
final class View
{
    private static string $layout = '';
    private static array $sections = [];
    private static array $sectionStack = [];

    public static function render(string $name, array $data = []): string
    {
        $previousLayout = self::$layout;
        self::$layout = '';

        $content = self::renderFile($name, $data);

        $layout = self::$layout;
        self::$layout = $previousLayout;

        if ($layout !== '') {
            $data['content'] = $content;
            return self::renderFile('layouts/' . $layout, $data);
        }

        return $content;
    }

    private static function renderFile(string $name, array $data): string
    {
        $path = base_path('views/' . $name . '.php');
        if (!is_file($path)) {
            throw new \RuntimeException("View bulunamadı: {$name}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        $self = new self();
        require $path;
        return (string) ob_get_clean();
    }

    /** Called inside a view to declare its layout. */
    public function layout(string $name): void
    {
        self::$layout = $name;
    }

    /** Begin capturing a named section. */
    public function section(string $name): void
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    /** Stop capturing the current section. */
    public function endSection(): void
    {
        $name = array_pop(self::$sectionStack);
        if ($name !== null) {
            self::$sections[$name] = (string) ob_get_clean();
        }
    }

    /** Output a previously captured section. */
    public function yield(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }

    /** Include a partial view inline. */
    public function partial(string $name, array $data = []): void
    {
        echo self::renderFile($name, $data);
    }
}
