<?php
/*
@name diaspoleed
@author Simounet <http://www.simounet.net>
@link https://github.com/Leed-market/diaspoleed
@licence GPLv3
@version 1.0.0
@description This plugin diaspoleed add an article sharing option from Leed to <a target="_blank" href="https://diaspora.org/">diaspora (v2)</a>.
*/

include( __DIR__ . '/classes/Diaspoleed.php' );

function diaspoleedPluginAddTo(&$event){
    $configurationManager = new Configuration();
    $configurationManager->getAll();
    $diaspoleed = new Diaspoleed();
    $diasporaUrl = $configurationManager->get($diaspoleed::CONFIG_FIELD);
    if( empty($diasporaUrl) ) {
        return false;
    }

    echo '<a
        title="'._t('P_DIASPOLEED_SHARE_WITH_DIASPORA').'"
        rel="noopener" target="_blank"
        href="'.$diasporaUrl.'bookmarklet?url='.$event->getLink().'&amp;title=' . $event->getTitle() . '&amp;notes=&amp;v=1&amp;noui=1&amp;jump=doclose"
    >'._t('P_DIASPOLEED_DIASPORA_EXCLAMATION').'</a>';
}

function diaspoleedPluginSettingsLink(&$myUser){
    echo '<li><a class="toggle" href="#diaspora-plugin">'._t('P_DIASPOLEED_DIASPORA').'</a></li>';
}

function diaspoleedPluginSettingsBlock(&$myUser){
    $configurationManager = new Configuration();
    $configurationManager->getAll();
    $diaspoleed = new Diaspoleed();
    echo '
    <section class="diaspora-plugin">
        <form action="action.php?action=' . Diaspoleed::ACTION_VALUE . '" method="POST">
        <h2>'._t('P_DIASPOLEED_PLUGIN_TITLE').'</h2>
        <p class="diasporaBlock">
        <label for="plugin_diaspora_url">'._t('P_DIASPOLEED_DIASPORA_LINK').'</label>
        <input type="text" placeholder="' . Diaspoleed::DEFAULT_VALUE. '" value="'.$configurationManager->get($diaspoleed::CONFIG_FIELD).'" id="plugin_diaspora_url" name="plugin_diaspora_url" />
        <input type="submit" class="button" value="'._t('P_DIASPOLEED_SAVE').'"><br/>
        </p>
        '._t('P_DIASPOLEED_NB_INFO').'
        </form>
    </section>
    ';
}

function diaspoleedPluginUpdateUrl($_){
    $myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);
    if($myUser===false) exit(_t('P_DIASPOLEED_CONNECTION_ERROR'));

    if(
        isset($_['action'])
        && $_['action'] === Diaspoleed::ACTION_VALUE
    ){
        $configurationManager = new Configuration();
        $diasporaUrl = $_['plugin_diaspora_url'];
        $diasporaUrl .= (substr($diasporaUrl, -1) === '/' ? '' : '/');
        $diaspoleed = new Diaspoleed();
        $configurationManager->change(
            ['value' => $diasporaUrl],
            ['key' => $diaspoleed::CONFIG_FIELD]
        );
        $_SESSION['configuration'] = null;
        header('location: settings.php');
    }
}

Plugin::addHook('event_post_top_options', 'diaspoleedPluginAddTo');
Plugin::addHook('setting_post_link', 'diaspoleedPluginSettingsLink');
Plugin::addHook('setting_post_section', 'diaspoleedPluginSettingsBlock');
Plugin::addHook("action_post_case", "diaspoleedPluginUpdateUrl");

?>
