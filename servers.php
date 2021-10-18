<?php

const front_server = "25.52.112.72"; // yasu
const rabbit_server = "25.77.32.88"; // jeff
const back_server = "25.52.190.242"; // yi-hsuan
const database_server = "25.52.70.25"; // jakub

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

// RABBITMQ USER ACCOUNT INFO //

//USER:backend
//PASSWORD: back

//USER: frontend
//PASSWORD: front

//USER: database
//PASSWORD: data