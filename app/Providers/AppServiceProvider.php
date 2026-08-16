<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\WhatsappInstance;
use App\Observers\NotificationObserver;
use App\Observers\WhatsappInstanceObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Types\BooleanType;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notification::observe(NotificationObserver::class);
        WhatsappInstance::observe(WhatsappInstanceObserver::class);

        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            });

        // Marca a API inteira como protegida por JWT. Os endpoints anotados
        // com @unauthenticated (login, register, webhook) ficam de fora.
        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });

        // O JwtAuthMiddleware devolve a resposta de 401 em vez de lançar
        // exceção, então o Scramble não consegue inferi-la sozinho. Aqui ela
        // é adicionada em todo endpoint que exige autenticação.
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            foreach ($openApi->paths as $path) {
                foreach ($path->operations as $operation) {
                    if ($operation->security === []) {
                        continue;
                    }

                    $operation->addResponse(
                        Response::make(401)
                            ->description('Token ausente, inválido ou expirado.')
                            ->setContent('application/json', Schema::fromType(
                                (new ObjectType)
                                    ->addProperty('error', new BooleanType)
                                    ->addProperty('message', (new StringType)->nullable(true))
                                    ->addProperty('data', (new StringType)->nullable(true))
                                    ->setRequired(['error', 'message', 'data'])
                            ))
                    );
                }
            }
        });
    }
}
