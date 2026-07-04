<?php
/**
 * Created by IntelliJ IDEA.
 * User: admin
 * Date: 19.08.2018
 * Time: 12:35
 */

$this->title = $model->title; ?>

<style>
    body {color: #4d4d4d}
    img {max-width: 100%}
    h1 {margin-bottom: 15px; padding-bottom: 5px; border-bottom: 1px #8e8e93 solid; font-size: 20px; margin-top: 10px;}

    ::-webkit-scrollbar {
        display: none;
    }
</style>

<div style="padding: 10px; text-align: justify">
    <?= $model->content; ?>
</div>


