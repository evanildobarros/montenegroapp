<?php if ($this->Analytics->getStatus()) { ?>
    <div class="row" id="graficos-analytics">
        <div class="panel panel-default col-xs-12 col-sm-12 col-md-12 col-lg-12 style-analytics">
            <h3>Visitas</h3>
            <h4>
                <small>últimos 30 dias</small>
            </h4>
            <div id="visitas"></div>
        </div>
        <div class="panel panel-default col-xs-12 col-sm-12 col-md-6 col-lg-4 style-analytics">
            <h3>Visitas por estados</h3>
            <h4>
                <small>últimos 30 dias</small>
            </h4>
            <div id="visitas-por-estados"></div>
        </div>
        <div class="panel panel-default col-xs-12 col-sm-12 col-md-6 col-lg-4 style-analytics">
            <h3>Visitas por navegadores</h3>
            <h4>
                <small>últimos 30 dias</small>
            </h4>
            <div id="visitas-por-navegadores"></div>
        </div>
        <div class="panel panel-default col-xs-12 col-sm-12 col-md-6 col-lg-4 style-analytics">
            <h3>Visitas por dispositivos</h3>
            <h4>
                <small>últimos 30 dias</small>
            </h4>
            <div id="visitas-por-dispositivos"></div>
        </div>
        <div class="panel panel-default col-xs-12 col-sm-12 col-md-6 col-lg-4 style-analytics no-margin-bottom">
            <h3>Páginas mais visitadas</h3>
            <h4>
                <small>últimos 30 dias</small>
            </h4>
            <div id="paginas-mais-visitadas"></div>
        </div>
        <div class="panel panel-default col-xs-12 col-sm-12 col-md-4 col-lg-2 style-analytics no-margin-bottom">
            <h3>Usuários ativos</h3>
            <h4>
                <small>no momento</small>
            </h4>
            <div id="usuarios-ativos" class="text-center"></div>
        </div>
        <div class="panel panel-default col-xs-12 col-sm-12 col-md-8 col-lg-6 style-analytics no-margin-bottom">
            <h3>Total de visitas</h3>
            <h4>
                <small>de 01/01/<?php echo date('Y'); ?> até <?php echo date('d/m/Y'); ?></small>
            </h4>
            <div id="total-visitas"></div>
        </div>
    </div>
    <script>
        (function (w, d, s, g, js, fs) {
            g = w.gapi || (w.gapi = {});
            g.analytics = {
                q: [], ready: function (f) {
                    this.q.push(f);
                }
            };
            js = d.createElement(s);
            fs = d.getElementsByTagName(s)[0];
            js.src = 'https://apis.google.com/js/platform.js';
            fs.parentNode.insertBefore(js, fs);
            js.onload = function () {
                g.load('analytics');
            };
        }(window, document, 'script'));

        gapi.analytics.ready(function () {
            gapi.analytics.auth.authorize({
                'serverAuth': {
                    'access_token': '<?php echo $this->Analytics->getToken(); ?>'
                }
            });

            var dataChartVisitas = new gapi.analytics.googleCharts.DataChart({
                query: {
                    'ids': 'ga:<?php echo $this->Analytics->getProfile_id(); ?>',
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'metrics': 'ga:visits',
                    'dimensions': 'ga:day'
                },
                chart: {
                    'container': 'visitas',
                    'type': 'LINE',
                    'options': {
                        'width': '100%'
                    }
                }
            });
            dataChartVisitas.execute();

            var dataChartVisitasPorEstados = new gapi.analytics.googleCharts.DataChart({
                query: {
                    'ids': 'ga:<?php echo $this->Analytics->getProfile_id(); ?>',
                    metrics: 'ga:sessions',
                    dimensions: 'ga:region',
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'max-results': 6,
                    sort: '-ga:sessions'
                },
                // chart: {
                //     container: 'visitas-por-estados',
                //     type: 'GEO',
                //     options: {
                //         width: '100%',
                //         region: 'BR',
                //         displayMode: 'markers',
                //     }
                // },
                chart: {
                    'container': 'visitas-por-estados',
                    'type': 'BAR',
                }
            });
            dataChartVisitasPorEstados.execute();

            var dataChartVisitasPorNavegadores = new gapi.analytics.googleCharts.DataChart({
                query: {
                    'ids': 'ga:<?php echo $this->Analytics->getProfile_id(); ?>',
                    'dimensions': 'ga:browser',
                    'metrics': 'ga:sessions',
                    'sort': '-ga:sessions',
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'max-results': '6'
                },
                chart: {
                    container: 'visitas-por-navegadores',
                    type: 'TABLE',
                    options: {
                        width: '100%'
                    }
                }
            });
            dataChartVisitasPorNavegadores.execute();

            var dataChartVisitasPorDispositivos = new gapi.analytics.googleCharts.DataChart({
                query: {
                    'ids': 'ga:<?php echo $this->Analytics->getProfile_id(); ?>',
                    'dimensions': 'ga:deviceCategory',
                    'metrics': 'ga:sessions',
                    'sort': '-ga:sessions',
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'max-results': '6'
                },
                chart: {
                    container: 'visitas-por-dispositivos',
                    type: 'PIE',
                    options: {
                        width: '100%',
                        'pieHole': 4 / 9
                    }
                }
            });
            dataChartVisitasPorDispositivos.execute();

            var dataChartPaginasMaisVisitas = new gapi.analytics.googleCharts.DataChart({
                query: {
                    'ids': 'ga:<?php echo $this->Analytics->getProfile_id(); ?>',
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'metrics': 'ga:pageviews',
                    'dimensions': 'ga:pageTitle',
                    'sort': '-ga:pageviews',
                    'max-results': 7
                },
                chart: {
                    'container': 'paginas-mais-visitadas',
                    'type': 'PIE',
                    'options': {
                        'width': '100%'
                    }
                }
            });
            dataChartPaginasMaisVisitas.execute();

            gapi.client.analytics.data.realtime
                .get({
                    ids: 'ga:<?php echo $this->Analytics->getProfile_id(); ?>',
                    metrics: 'rt:activeUsers'
                })
                .then(function (response) {
                    $('#usuarios-ativos').text(typeof response.result.rows == 'undefined' ? 0 : response.result.rows[0]);
                });


            var dataChartTotalVisitas = new gapi.analytics.googleCharts.DataChart({
                query: {
                    'ids': 'ga:<?php echo $this->Analytics->getProfile_id(); ?>',
                    'metrics': 'ga:visits',
                    'dimensions': 'ga:month',
                    'start-date': '<?php echo date("Y"); ?>-01-01',
                    'end-date': 'today',
                    'sort': 'ga:month'
                },
                chart: {
                    'container': 'total-visitas',
                    'type': 'COLUMN',
                    'options': {
                        'width': '100%'
                    }
                }
            });
            dataChartTotalVisitas.execute();
        });
    </script>
<?php } ?>
