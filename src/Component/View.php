<?php

namespace Gietos\Kicker\Component;

class View
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $params = [];

    public function __construct(string $template, array $params = [])
    {
        $this->template = $template;
        $this->params = $params;
    }

    public function render(\Twig_Environment $twig)
    {
        return $twig->render($this->template, $this->params);
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setParam(string $name, $value)
    {
        $this->params[$name] = $value;
    }
}
