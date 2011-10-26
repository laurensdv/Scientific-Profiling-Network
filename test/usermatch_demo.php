<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<?php
require_once '../SMNIncludes.php';
require_once '../view/show_array.php';
require_once '../analysis/includes.php';
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>User Match Demonstration</title>
        <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <style>
            article, aside, details, figcaption, figure, footer, header,
            hgroup, menu, nav, section { display: block; }
        </style>
        <link rel="stylesheet" type="text/css" media="screen,projection" href="css/admin.css" />
    </head>
    <body>
        <div id="main">
        <div id="middle">
            <div id="center-column">
                <div class="top-bar">
                    <h1>Demo: User Matching</h1>
                </div>
                <h2>Matching conferences</h2>
                <div class ="table">
                        <?php
                        $uri;
                        $res1;
                        if(!array_key_exists('q2', $_GET)) $user2 = 'mebner';
                            else $user2 = $_GET['q2'];
                        if ($_GET != null) {
                            $res1 = SMNUserQueries::tags($_GET['q']);
                            $res2 = SMNUserQueries::tags($user2);
                            $result = array_intersect($res1, $res2);
                            $sim = cosineSimilarity2($res1, $res2);
                            $conferences = array();
                            foreach($result as $key => $tag) {
                                if(SMNConference::isConference($tag)) {
                                    $conferencedata = SMNConference::findConference($tag);
                                    $conferencename = $conferencedata['name'];
                                    $conferences[$tag] = $conferencename;
                                }

                            }
                            echo "<p>Cosine Similarity between <strong>" .$_GET['q']."</strong> and <strong>".$user2."</strong> is <strong>".$sim."</strong></p>";
                            html_show_array($conferences);
                            echo '</div><h2>'.'Matching Tags'.'</h2><div class ="table">';
                            html_show_array($result);
                            echo '</div><h2>'.$_GET['q'].'</h2><div class ="table">';
                            html_show_array($res1);
                            echo '</div><h2>'.$user2.'</h2><div class ="table">';
                            html_show_array($res2);


                        } else
                            echo "Not found";

                        ?>
                </div>
            </div>
            <div id="right-column">
                <strong class="h">Quick Info</strong>
                <div class="box">This is a matching between two twitter users.</div>
            </div>
        </div>
        </div>
    </body>
</html>
