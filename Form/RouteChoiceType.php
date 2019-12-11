<?php

namespace Opera\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RouteChoiceType extends AbstractType
{
    private $routes;

    public function __construct(RouterInterface $router)
    {
        foreach ($router->getRouteCollection()->all() as $routeName => $route) {
            if ($route->getDefault("_controller")) {
                $controllerName = $this->getControllerName($route->getDefault("_controller"));
                if ($controllerName) {
                    $this->routes[$controllerName][$routeName] = $routeName;
                } else {
                    $this->routes["Other"][$routeName] = $routeName;
                }
            }

        }
    }

    /**
     * ex:
     *  input -> "Opera\AdminBundle\Controller\AdminPagesController::layout"
     *  output -> "AdminPagesController"
     */
    private function getControllerName(string $controllerRoutePath)
    {
        preg_match('/.*\\\\(.*)::.*/', $controllerRoutePath, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        return $matches[1];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->routes,
        ]);
    }

    public function getParent()
    {
        return \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class;
    }
}
