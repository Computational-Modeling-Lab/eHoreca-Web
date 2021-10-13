<!DOCTYPE html>
<html class="st-layout ls-top-navbar ls-bottom-footer show-sidebar sidebar-l1 sidebar-r1-xs" lang="en">

<head>
    @include('includes/head')
</head>

<body>
    <!-- Wrapper required for sidebar transitions -->
    <div class="st-container">
        @include('navbar')
        <!-- Sidebar component with st-effect-1 (set on the toggle button within the navbar) -->
        <aside class="sidebar left sidebar-size-1 sidebar-mini-reveal sidebar-offset-0 sidebar-skin-dark sidebar-visible-desktop" id=sidebar-menu data-type=collapse>
            <div data-scrollable>
                <ul class="sidebar-menu sm-bordered sm-icons-block sm-icons-right">
                    <li class="hasSubmenu open">
                        <a href="#dumbsters"><i class="fa fa-folder"></i><span class="categoryTitleSpan">Cleaning services</span></a>
                        <ul id="dumbsters" class="in">
                            <li id="mixed"><a onclick="getBins('mixed')"><i class="fa fa-check layerTitleIcon"></i><span class="layerTitleSpan">Mixed garbage dumbsters</span></a></li>
                            <li id="recyclable"><a onclick="getBins('recyclable')"><i class="fa fa-check layerTitleIcon"></i><span id="bins-recyclable" class="layerTitleSpan">Recyclable garbage dumpsters</span></a></li>
                            {{-- <li id="layer-3"><a href="javascript:toggleLayer(3)"><i class="fa fa-check layerTitleIcon"></i><span class="layerTitleSpan">Stations</span></a></li>
                            <li id="layer-4"><a href="javascript:toggleLayer(4)"><i class="fa fa-check layerTitleIcon"></i><span class="layerTitleSpan">Re-loading stations</span></a></li> --}}
                            <li id="compost"><a onclick="getBins('compost')"><i class="fa fa-check layerTitleIcon"></i><span id="bins-compost" class="layerTitleSpan">Compost dumbsters</span></a></li>
                            <li id="glass"><a onclick="getBins('glass')"><i class="fa fa-check layerTitleIcon"></i><span id="bins-compost" class="layerTitleSpan">Glass dumbsters</span></a></li>
                            <li id="metal"><a onclick="getBins('metal')"><i class="fa fa-check layerTitleIcon"></i><span id="bins-compost" class="layerTitleSpan">Metal dumbsters</span></a></li>
                            <li id="paper"><a onclick="getBins('paper')"><i class="fa fa-check layerTitleIcon"></i><span id="bins-compost" class="layerTitleSpan">Paper dumbsters</span></a></li>
                            <li id="plastic"><a onclick="getBins('plastic')"><i class="fa fa-check layerTitleIcon"></i><span id="bins-compost" class="layerTitleSpan">Plastic dumbsters</span></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </aside>

        <div id="main-map"></div>
        @include('footer')

    </div>
    @include('includes/scripts', ['includeMap' => true])
</body>

</html>
