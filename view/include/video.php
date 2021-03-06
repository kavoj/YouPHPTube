<?php
$playNowVideo = $video;
$transformation = "{rotate:" . $video['rotation'] . ", zoom: " . $video['zoom'] . "}";

if ($video['rotation'] === "90" || $video['rotation'] === "270") {
    $aspectRatio = "9:16";
    $vjsClass = "vjs-9-16";
    $embedResponsiveClass = "embed-responsive-9by16";
} else {
    $aspectRatio = "16:9";
    $vjsClass = "vjs-16-9";
    $embedResponsiveClass = "embed-responsive-16by9";
}
?>
<div class="row main-video" id="mvideo">
    <div class="col-sm-2 col-md-2 firstC"></div>
    <div class="col-sm-8 col-md-8 secC">
        <div id="videoContainer">
            <div id="floatButtons" style="display: none;">
                <p class="btn btn-outline btn-xs move">
                    <i class="fas fa-expand-arrows-alt"></i>
                </p>
                <button type="button" class="btn btn-outline btn-xs"
                        onclick="closeFloatVideo();floatClosed = 1;">
                    <i class="far fa-window-close"></i>
                </button>
            </div>
            <div id="main-video" class="embed-responsive <?php echo $embedResponsiveClass; ?>">
                <video preload="auto" poster="<?php echo $poster; ?>" controls class="embed-responsive-item video-js vjs-default-skin <?php echo $vjsClass; ?> vjs-big-play-centered" id="mainVideo" data-setup='{ "aspectRatio": "<?php echo $aspectRatio; ?>" }'>
                    <?php if ($playNowVideo['type'] == "video") { ?>
                        <!-- <?php echo $playNowVideo['title'], " ", $playNowVideo['filename']; ?> -->
                        <?php echo getSources($playNowVideo['filename']);
                    } else {
                        ?>
                        <source src="<?php echo $playNowVideo['videoLink']; ?>" type="video/mp4" >
                    <?php } ?>
                    <p><?php echo __("If you can't view this video, your browser does not support HTML5 videos"); ?></p>
                    <p class="vjs-no-js"><?php echo __("To view this video please enable JavaScript, and consider upgrading to a web browser that"); ?>
                        <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>
                </video>
                <?php
                require_once $global['systemRootPath'] . 'plugin/YouPHPTubePlugin.php';
                // the live users plugin
                if (YouPHPTubePlugin::isEnabled("0e225f8e-15e2-43d4-8ff7-0cb07c2a2b3b")) {
                    require_once $global['systemRootPath'] . 'plugin/VideoLogoOverlay/VideoLogoOverlay.php';
                    $style = VideoLogoOverlay::getStyle();
                    $url = VideoLogoOverlay::getLink();
                    ?>
                    <div style="<?php echo $style; ?>">
                        <a href="<?php echo $url; ?>"> <img src="<?php echo $global['webSiteRootURL']; ?>videos/logoOverlay.png"></a>
                    </div>
<?php } ?>

            </div>
        </div>
<?php if ($config->getAllow_download()) { ?>
                <?php if ($playNowVideo['type'] == "video") { ?>
                <a class="btn btn-xs btn-default pull-right " role="button" href="<?php echo $global['webSiteRootURL'] . "videos/" . $playNowVideo['filename']; ?>.mp4" download="<?php echo $playNowVideo['title'] . ".mp4"; ?>" >
                    <i class="fa fa-download"></i>
                <?php echo __("Download video"); ?>
                </a>
                <?php } else { ?>
                <a class="btn btn-xs btn-default pull-right " role="button" href="<?php echo $video['videoLink']; ?>" download="<?php echo $playNowVideo['title'] . ".mp4"; ?>" >
                    <i class="fa fa-download"></i>
                <?php echo __("Download video"); ?>
                </a>      

    <?php }
} ?>
    </div>
    <div class="col-sm-2 col-md-2"></div>
</div>
<!--/row-->
<script>
<?php $_GET['isMediaPlaySite'] = $playNowVideo['id']; ?>

    var mediaId = <?php echo $playNowVideo['id']; ?>;

    var player;
    $(document).ready(function () {

<?php
if ($playNowVideo['type'] == "linkVideo") {
    echo '$("time.duration").hide();';
}

if (!$config->getAllow_download()) {
    ?>
            // Prevent HTML5 video from being downloaded (right-click saved)?
            $('#mainVideo').bind('contextmenu', function () {
                return false;
            });
<?php } ?>
        player = videojs('mainVideo');
        player.zoomrotate(<?php echo $transformation; ?>);
        player.on('play', function () {
            addView(<?php echo $playNowVideo['id']; ?>);
        });
        player.ready(function () {
<?php if ($config->getAutoplay()) {
    ?>
                setTimeout(function () {
                    if (typeof player === 'undefined') {
                        player = videojs('mainVideo');
                    }
                    try {
                        player.play();
                    } catch (e) {
                        setTimeout(function () {
                            player.play();
                        }, 1000);
                    }
                }, 150);
    <?php } else {
    ?>
                if (Cookies.get('autoplay') && Cookies.get('autoplay') !== 'false') {
                    setTimeout(function () {
                        if (typeof player === 'undefined') {
                            player = videojs('mainVideo');
                        }
                        try {
                            player.play();
                        } catch (e) {
                            setTimeout(function () {
                                player.play();
                            }, 1000);
                        }
                    }, 150);
                }
<?php }
?>
            this.on('ended', function () {
                console.log("Finish Video");
<?php // if autoplay play next video
if (!empty($autoPlayVideo)) {
    ?>
                    if (Cookies.get('autoplay') && Cookies.get('autoplay') !== 'false') {
                        document.location = '<?php echo $autoPlayVideo['url']; ?>';
                    }
<?php } ?>

            });
        });
        player.persistvolume({
            namespace: "YouPHPTube"
        });
    });
</script>
