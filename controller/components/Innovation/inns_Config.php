<?php

const INNS_CONFIG_PATH = '/../../../config.php';







const INNS_MODEL_INNOVATION  = '/../../../model/Innovation/Innovation.php';
const INNS_MODEL_CATEGORY    = '/../../../model/Innovation/Category.php';
const INNS_MODEL_COMMENT     = '/../../../model/Innovation/CommentModel.php';
const INNS_MODEL_ATTACHMENT  = '/../../../model/Innovation/AttachmentModel.php';
const INNS_MODEL_VOTE        = '/../../../model/Innovation/VoteModel.php';


const INNS_CTRL_INNOVATION = '/../../../controller/components/Innovation/InnovationController.php';
const INNS_CTRL_CATEGORY   = '/../../../controller/components/Innovation/CategoryController.php';
const INNS_CTRL_COMMENT    = '/../../../controller/components/Innovation/CommentController.php';
const INNS_CTRL_VOTE        = '/../../../controller/components/Innovation/VoteController.php';


const INNS_VIEW_ADMIN  = '/../../../view/Admin/Innovation/src/';
const INNS_VIEW_CLIENT = '/../../../view/Client/Innovation/src/';
// CONFIG DB
require_once __DIR__ . INNS_CONFIG_PATH;
// MODELS
require_once __DIR__ . INNS_MODEL_INNOVATION;
require_once __DIR__ . INNS_MODEL_CATEGORY;
require_once __DIR__ . INNS_MODEL_COMMENT;
require_once __DIR__ . INNS_MODEL_ATTACHMENT;
require_once __DIR__ . INNS_MODEL_VOTE;

// CONTROLLERS
require_once __DIR__ . INNS_CTRL_INNOVATION;
require_once __DIR__ . INNS_CTRL_CATEGORY;
require_once __DIR__ . INNS_CTRL_COMMENT;
require_once __DIR__ . INNS_CTRL_VOTE;
