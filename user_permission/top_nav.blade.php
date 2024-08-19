{{-- @extends('backend.layouts.master')

@section('content') --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
<style>
    .sidebar-menu li:hover>a {
        background-color: #f1f1f1;
        /* Change this to your desired highlight color */
        font-weight: bold;
    }

    .treeview-menu-1 {
        display: none;
    }

    .treeview-menu-1 {
        padding-left: 20px;
    }

    .treeview-1.active>.treeview-menu-1 {
        display: block;
    }

    .sidebar-menu .active>a {
        background-color: rgb(191, 191, 46) !important;
        font-weight: bold;
    }

    .treeview-menu-1 .active>a {
        background-color: #cccccc !important;
        font-weight: bold;
    }

    li a {
        color: #000000 !important;
    }

    .multinav-scroll-1 {
        overflow-y: scroll;
        max-height: calc(100vh - 200px);
    }


    /* Styles for collapsible sidebar */
    .sidebar-collapsed {
        width:56px;
        overflow: hidden;
        position: relative;
        /* padding: 1px 10px; */
        margin: 0 0px;
    }

    .sidebar-collapsed .treeview-menu-1 {
        display: none !important;
    }

    .sidebar-collapsed .treeview-1:hover>.treeview-menu-1 {
        display: block !important; 
        width: 250px;
        background: #fff;
        padding: 10px 30px;         
    }
    /* .sidebar-collapsed .treeview-menu-1:hover {
        background: #fff;
        padding: 10px 30px;        
    } */
    .sidebar-collapsed .treeview-1>a>i {
        margin-right: 0;
    }

    .multinav-scroll-1 {
        overflow-y: scroll;
        max-height: calc(100vh - 200px);
    }
</style>
<div id="sidebar">
    {{-- @foreach ($menuItems as $menuItem)
            <li>
                <a href="{{ $menuItem->menu_link }}">
                    @if ($menuItem->menu_icon)
                        <i class="{{ $menuItem->menu_icon }}"></i>
                    @endif
                    {{ $menuItem->menu_name }}
                </a>
                @if (isset($menuItem->children))
                    <ul>
                        @foreach ($menuItem->children as $child)
                            <li>
                                <a href="{{ $child->menu_link }}">
                                    @if ($child->menu_icon)
                                        <i class="{{ $child->menu_icon }}"></i>
                                    @endif
                                    {{ $child->menu_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach --}}
</div>

<script>
    // $(document).ready(function() {
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     bind_top_nav();
    // });

    // function bind_top_nav() {
    //     $.ajax({
    //             type: 'GET',
    //             url: "{{ route('get-sidebar.data') }}",
    //             dataType: 'json',
    //         })
    //         .done(function(response) {
    //             $("#top_nav").html(response.html);
    //         })
    //         .fail(function(xhr, status, error) {
    //             console.error('AJAX Error: ', status, error);
    //         });
    // }
</script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        loadSidebarMenu();

        $('#toggle-sidebar').on('click', function() {
            $('#side_bar').toggleClass('sidebar-collapsed');
            if ($('#side_bar').hasClass('sidebar-collapsed')) {
                $('.multinav-scroll').removeClass('multinav-scroll-1');
            } else {
                $('.multinav-scroll').addClass('multinav-scroll-1');
            }
        });

    });

    function loadSidebarMenu() {
        $.ajax({
            url: '{{ route('get-sidebar.data') }}',
            method: 'GET',
            success: function(response) {
                $('#sidebar').html(response.html);

                // Initialize the sidebar functionality
                $('.sidebar-menu .treeview-1 > a').on('click', function(e) {
                    $('.multinav-scroll').addClass('multinav-scroll-1');
                    // var style = document.createElement('style');
                    // style.type = 'text/css';
                    // style.innerHTML= `
                    //     .multinav-scroll {
                    //         overflow-y: scroll;
                    //         max-height: calc(100vh - 200px); 
                    //     }`;
                    // document.head.appendChild(style);
                    var $this = $(this);
                    var $parent = $this.parent();
                    var $submenu = $parent.find('.treeview-menu-1').first();

                    if ($submenu.length) {
                        e.preventDefault(); // Prevent default only if there is a submenu

                        if ($parent.hasClass('active')) {
                            $parent.removeClass('active');
                        } else {
                            $parent.siblings().removeClass('active');
                            $parent.addClass('active');
                        }
                    }
                });

                // Highlight the active link
                var currentUrl = window.location.href;
                $('.sidebar-menu .treeview-1 a').each(function() {
                    if (this.href === currentUrl) {
                        $(this).addClass('active');
                        $(this).closest('.treeview-1').addClass('active');
                    }
                });
                // var currentUrl_1 = window.location.href;
                $('.sidebar-menu .treeview-menu-1 li a').each(function() {
                    if (this.href === currentUrl) {
                        $(this).addClass('active');
                        $(this).closest('.treeview-menu-1 li').addClass('active');
                        $('.multinav-scroll').addClass('multinav-scroll-1');
                    }
                });
            },
            error: function(xhr) {
                console.error(xhr);
            }
        });
    }
</script>
{{-- @endsection --}}
