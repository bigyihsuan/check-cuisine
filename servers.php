<?php
const front_server = "25.53.122.72"; // yasu
const rabbit_server = "25.6.107.45"; // jeff (cluster2)
const back_server = "25.51.190.242"; // yi-hsuan
const database_server = "25.53.49.9"; // jakub

const back_server_creds = ["backend", "back"];
const front_server_creds = ["frontend", "front"];
const data_server_creds = ["database", "data"];

const FRONT_BACK = 'front-back';
const BACK_FRONT = 'back-front';
const BACK_DATA = 'back-data';
const DATA_BACK = 'data-back';

abstract class Prefix
{
    const LOGIN = 0;
    const REGISTER = 1;
    const GET = 2;
    const SET = 3;
    const DELETE = 4;
}

const API_KEY_FDC = "ZGhr5eocxFcAba6dlGsB4pxfBGIJfDlnCfBo4mfl";

// RABBITMQ USER ACCOUNT INFO //

//USER:backend
//PASSWORD: back

//USER: frontend
//PASSWORD: front

//USER: database
//PASSWORD: data
