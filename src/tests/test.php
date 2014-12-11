<?php
$folder = scandir("./");
$all_movies = array();
foreach ($folder as $f) {
    $t = explode(".", $f);
    if (strtolower($t[count($t) - 1]) == "mp4") {
        $all_movies[$t[0]] = json_decode(file_get_contents("http://www.omdbapi.com/?i=" . $t[0] . "&plot=short&r=json"));
    }
}
?>
<?php foreach ($all_movies as $tt => $m): ?>
    <div>
        <a href="<?= $tt; ?>.mp4"><img src="http://img.omdbapi.com/?i=<?= $tt; ?>&apikey=739f286f" itemprop="image"/>
            <br/><b><?= $m->Title; ?></b> (<?= $m->Year; ?>)<br/>
            <span><?= $m->imdbRating; ?>/10 (<?= $m->imdbVotes; ?> votes)</span></a>
    </div>
<?php endforeach; ?>
