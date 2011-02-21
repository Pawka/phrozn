<?php

/* tpl1.twig */
class __TwigTemplate_60c28df49e4c0c61b466a259615b72d3 extends Twig_Template
{
    public function display(array $context)
    {
        // line 1
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\">
<html lang=\"en\">
  <head>
    <title>My Webpage</title>
  </head>
  <body>
    <ul id=\"navigation\">
    ";
        // line 8
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_iterator_to_array((isset($context['navigation']) ? $context['navigation'] : null));
        $countable = is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable);
        $length = $countable ? count($context['_seq']) : null;
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if ($countable) {
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context['_key'] => $context['item']) {
            echo "
      <li><a href=\"";
            // line 9
            echo $this->getAttribute((isset($context['item']) ? $context['item'] : null), "href", array(), "any");
            echo "\">";
            echo $this->getAttribute((isset($context['item']) ? $context['item'] : null), "caption", array(), "any");
            echo "</a></li>
    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if ($countable) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 10
        echo "
    </ul>

    <h1>My Webpage</h1>
    ";
        // line 14
        echo (isset($context['a_variable']) ? $context['a_variable'] : null);
        echo "
  </body>
</html>
";
    }

}
