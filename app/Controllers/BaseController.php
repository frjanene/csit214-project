<?php
class BaseController {
  protected function render(string $template, string $title, string $layout = 'main', array $data = []): void {
    $templateVar = $template;
    $vars = array_merge($data, [
      'title'    => $title,
      'layout'   => $layout,
      'template' => $templateVar,
    ]);
    view($template, $vars);
  }
}
