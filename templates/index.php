<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Blacklist</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <style type="text/css">
            * {
                font-size: 12px;
            }
            div.box {
                left: 50%;
                padding: 0;
                margin-left: -168px;
            }
        </style>
    </head>
    <body>
        <div class="box col-md-3">
            <h1>Blacklist</h1>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Consultar CPF</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Busca" />
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
                    </div>
                </div>
            </div>
            <?php if (isset($feedback)): ?>
                <div class="alert alert-danger">
                    <strong><?php print $feedback; ?></strong>
                </div>
            <?php endif; ?>
            <div id="feedback" class="alert alert-danger" style="display: none;">
                <strong>&nbsp;</strong>
                <a href="#" class="alert-link pull-right">&nbsp;</a>
            </div>
        </div>
        <!-- Scripts -->
        <script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
        <script type="text/javascript">
            $(function () {
                var CPF;
                $('div.panel-body input').mask('000.000.000-00', {
                    reverse: true
                });
                $('div.panel-body button').unbind('click').click(function () {
                    CPF = $(this).closest('div').find('input').val().replace(/\D/g, '');
                    $.ajax({
                        url: '/consulta?cpf=' + CPF,
                        type: 'GET',
                        success: function (data, status, jqXHR) {
                            statusCPF(true, jqXHR.responseJSON.message);
                        },
                        error: function (jqXHR) {
                            statusCPF(false, jqXHR.responseJSON.message);
                        }
                    });
                });
                var statusCPF = function (status, message) {
                    var feedback = $("div#feedback");
                    var action = (status ? 'del' : 'add');
                    feedback.removeClass()
                            .addClass('alert alert-' + (status ? 'success' : 'danger'));
                    feedback.find('strong')
                            .text(message);
                    feedback.find('a')
                            .removeClass()
                            .addClass('alert-link pull-right ' + action)
                            .text(status ? 'Remover' : 'Incluir');
                    $('a.' + action).unbind('click').click(function () {
                        console.log(action);
                        var settings;
                        if (status) {
                            settings = {
                                'async': true,
                                'crossDomain': true,
                                'url': '/',
                                'method': 'DELETE',
                                'headers': {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'cache-control': 'no-cache'
                                },
                                'data': {
                                    'cpf': CPF
                                }
                            };
                        } else {
                            form = new FormData();
                            form.append('cpf', CPF);
                            settings = {
                                'async': true,
                                'crossDomain': true,
                                'url': '/',
                                'method': 'POST',
                                'processData': false,
                                'contentType': false,
                                'mimeType': 'multipart/form-data',
                                'data': form
                            };
                        }
                        $.ajax(settings).success(function (data, status, jqXHR) {
                            console.log(jqXHR);
                            statusCPF(true, jQuery.parseJSON(jqXHR.responseText).message);
                        }).error(function (jqXHR) {
                            console.log(jqXHR);
                            statusCPF(false, jQuery.parseJSON(jqXHR.responseText).message);
                        });
                    });
                    feedback.show();
                };
            });
        </script>
    </body>
</html>
