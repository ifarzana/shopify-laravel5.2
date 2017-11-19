<?php
$configuration_menu =  NavigationManagerHelper::getConfigurationMenu();

?>

<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">

            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>

            <form role="search" class="navbar-form-custom" action="/search_results.html">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                </div>
            </form>
        </div>
        <ul class="nav navbar-top-links navbar-right">

            <?php if(!empty($configuration_menu)): ?>

                <?php $count1 = 0 ?>
                <?php if(empty($configuration_menu['pages'])): ?>
                    <!-- FIX-->
                <?php else: ?>
                    <li class="dropdown" <?php if($configuration_menu['active']) { echo ' '; } ?>>
                        <a class="dropdown-toggle count-info" data-toggle="dropdown">
                            <i class="<?php echo $configuration_menu['icon']; ?>"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-config">

                            <?php foreach($configuration_menu['pages'] as $page): ?>

                            <?php if(!$page['visible']) continue; ?>

                            <li>
                                <a href="<?php echo UrlHelper::getUrl($page['controller'], $page['action']) ?>">
                                    <div>
                                        <?php echo $page['title']; ?><span class="pull-right text-muted small"></span>
                                    </div>
                                </a>
                            </li>

                            <li class="divider"></li>
                            <?php endforeach ?>

                        </ul>
                    </li>
                <?php endif; ?>
                <?php $count1++ ?>
            <?php endif; ?>

            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <i class="fa fa-envelope"></i>  <span class="label label-warning">16</span>
                </a>
                <ul class="dropdown-menu dropdown-messages">
                    <li>
                        <div class="dropdown-messages-box">
                            <div class="media-body">
                                <small class="pull-right">46h ago</small>
                                <strong>Mike Loreipsum</strong> started following <strong>Monica Smith</strong>. <br>
                                <small class="text-muted">3 days ago at 7:58 pm - 10.06.2014</small>
                            </div>
                        </div>
                    </li>

                    <li class="divider"></li>
                    <li>
                        <div class="text-center link-block">
                            <a href="mailbox.html">
                                <i class="fa fa-envelope"></i> <strong>Read All Messages</strong>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>

                <?php

                $alerts = \App\Helpers\AlertHelper::getAlerts();

                /*Count the total number of alerts*/
                $count = 0;
                if(count($alerts['alerts']) > 0) {
                    foreach($alerts['alerts'] as $category_name => $alerts_array){
                        foreach($alerts_array as $alert){
                            $count+= $alert['count'];
                        }
                    }

                }

                ?>

                @if(AclManagerHelper::hasPermission('read', 'alerts'))
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell"></i>

                            @if($count>0)
                                <span class="label label-primary">{{$count}}</span>
                            @endif
                        </a>
                        @if(count($alerts['alerts']) > 0)
                            <ul class="dropdown-menu dropdown-alerts">
                                @foreach($alerts['alerts'] as $category_name => $alerts_array)
                                    @foreach($alerts_array as $alert)

                                    <li>
                                        <a href="<?php echo UrlHelper::getUrl('Alert\AlertController', 'index', array('key' => $alert['key'])); ?>">
                                            <div>
                                               {{ $alert['name'] }}
                                                <span class="pull-right text-muted small">{{ $alert['count'] }}</span>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="divider"></li>
                                    @endforeach

                                @endforeach

                            </ul>
                        @else
                            <ul class="dropdown-menu dropdown-alerts">
                                <li>No alerts to display</li>
                            </ul>
                        @endif
                    </li>
                @endif

            <li>
                <a href="{{ url('auth/logout') }}">
                    <i class="fa fa-sign-out"></i> Log out
                </a>
            </li>
            <li>
                <a class="right-sidebar-toggle">
                    <i class="fa fa-tasks"></i>
                </a>
            </li>
        </ul>

    </nav>
</div>
