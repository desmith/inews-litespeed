<?php 
    $url = site_url();
?>
<header class="isk_header">
    <div class="top">
        <div class="main-container">
            <div class="inner-container">
                <div class="top_contentwrap">
                    <!-- <div class="deskham">
                        <div></div>
                    </div> -->
                    <div class="logo_area">
                        <a href="<?php bloginfo('url'); ?>/" class="iskcon__logo">
                            <img src="<?php bloginfo('template_url'); ?>/assets/img/biglogo.png?v=1" class="logo__desktop" alt="ISKCON News" title="ISKCON News"/>
                            <img src="<?php bloginfo('template_url'); ?>/assets/img/smalllogo.png?v=1" class="logo__mobile" alt="ISKCON News" title="ISKCON News"/>
                        </a>
                    </div>
                    <div class="right">
                        <p>Founder Acharya His Divine Grace<br>
                            A.C. Bhaktivedanta Swami Prabhupada</p>
                    </div>
                    <div class="Prabhupada_image">
                        <img src="<?php bloginfo('template_url'); ?>/assets/img/prabhupad.png" alt="" title="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom">
        <div class="main-container">
            <div class="inner-container">
                <nav>
                    <a href="javascript:" class="site__menu-btn"> 
                        <span class="first"></span>
                        <span class="second"></span>
                        <span class="third"></span>
                    </a>

                    <?php
                            $header_menu_args = [
                            'theme_location'  => 'header_menu',
                            'container'       => false,
                            'menu_id'         => '',
                            'menu_class'      => 'site__menu',     
                            'depth'           => 2,
                            'walker'          => new DDM_Walker_Nav_Menu
                            ];
                            wp_nav_menu($header_menu_args);
                    ?>
                    
                </nav>
                <a href="javascript:void(0);" class="search_btn search-toggle"></a>
                <div class="nav_btnG">
                    <a href="<?php echo $url; ?>/donation-page/" class="btn hvr:outline bg--yellow c--white hvr:bg--transparent hvr:c--yellow">Donate Now</a>
                    <a href="<?php echo $url; ?>/submit-your-story/" class="btn outline bg--transparent c--black hvr:bg--blue hvr:c--white">Submit Story</a>
                </div>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="inner-container search_innercont">
            <form class="searchForm" id="searchForm" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" name="s" class="search-input" id="search_keyword" placeholder="Search..." value="<?php echo get_search_query(); ?>" autocomplete="off">
                <button type="submit" id="search_btn" class="search-btn"></button>
                <a href="javascript:" class="search-close search-toggle"></a>
            </form>
        </div>
    </div>
</header>