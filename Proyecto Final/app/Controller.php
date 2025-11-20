<?php
class Controller
{
    public function render($view, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/header.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<h3>View not found: $view</h3>";
        }
        require __DIR__ . '/../views/layouts/footer.php';
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
