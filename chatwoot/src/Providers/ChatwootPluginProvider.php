<?php

namespace Boy132\Chatwoot\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ChatwootPluginProvider extends ServiceProvider {
    public function boot(): void {
        $baseUrl = config('chatwoot.base_url');
        $websiteToken = config('chatwoot.website_token');

        if ($baseUrl && $websiteToken) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::STYLES_BEFORE,
                fn () => Blade::render(<<<'HTML'
                <!--Start of Chatwoot Script-->
                <script type="text/javascript">
                    (function(d,t) {
                        var BASE_URL="{{ $baseUrl }}";
                        var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
                        g.src=BASE_URL+"/packs/js/sdk.js";
                        g.defer = true;
                        g.async = true;
                        s.parentNode.insertBefore(g,s);
                        g.onload=function(){
                            window.chatwootSDK.run({
                                websiteToken: '{{ $websiteToken }}',
                                baseUrl: BASE_URL
                            })
                        }
                    })(document,"script");
                </script>
                <!--End of Chatwoot Script-->
            HTML, [
                    'baseUrl' => $baseUrl,
                    'websiteToken' => $websiteToken,
                ])
            );
        }
    }
}
