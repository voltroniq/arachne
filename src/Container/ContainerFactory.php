<?php
declare(strict_types=1);

namespace Arachne\Container;

// Import necessary classes from the PHP-DI library
use DI\Container;
use DI\ContainerBuilder;

final class ContainerFactory
{
    /**
     * This method creates and returns a configured PHP-DI container.
     *
     * The container is responsible for managing your application's dependencies.
     * It can automatically inject services into your classes (using autowiring).
     *
     * @param array $definitions Optional array of custom service definitions to override the defaults.
     * @return Container The fully built Dependency Injection container.
     */
    public static function create(array $definitions = []): Container
    {
        // Create an instance of the ContainerBuilder
        // The ContainerBuilder is used to configure the container (manage services and dependencies).
        $builder = new ContainerBuilder();

        // Check if 'useAutowiring' method is available, and call it if possible
        // Autowiring allows PHP-DI to automatically inject dependencies into your classes.
        // This check ensures compatibility with different versions of the PHP-DI library.
        if (method_exists($builder, 'useAutowiring')) {
            // Enable autowiring (PHP-DI will automatically resolve dependencies)
            $builder->useAutowiring(true);
        }

        // Avoid using 'useAnnotations()' or 'useAttributes()' here
        // These methods depend on specific versions of PHP-DI and the required annotation libraries.
        // Calling them without the proper configuration may cause compatibility issues.
        // If you need annotations or attributes, install the necessary libraries and enable them explicitly.

        // Merge custom definitions with the default definitions
        // $definitions is an optional parameter that allows the caller to pass custom service definitions.
        // We combine the default definitions (like settings) with any custom ones the user has provided.
        $all = array_merge(self::defaultDefinitions(), $definitions);

        // Add all definitions to the container
        // Add the merged definitions to the container. This makes the services (like settings) available for injection.
        $builder->addDefinitions($all);

        // Step 6: Build and return the container
        // Finally, we call the build() method to create the fully configured container.
        // The container will now be able to manage dependencies and resolve services.
        return $builder->build();
    }

    /**
     * Default service definitions for the container.
     *
     * @return array Default settings and services that should always be available in the container.
     */
    private static function defaultDefinitions(): array
    {
        // Return an array with default service definitions
        // These are services that the container will manage, such as app settings.
        return [
            'settings' => [
                'app_name' => 'Arachne',  // The default application name
                'env' => getenv('APP_ENV') ?: 'development', // Environment (defaults to 'development' if not set)
            ],
        ];
    }
}