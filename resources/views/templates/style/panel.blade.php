<style>
    /*Panel*/

    .cipanel {
        clear: both;
        margin-bottom: 25px;
        margin-top: 0;
        padding: 0;
        background-color: #fff;
        box-shadow: 0 1px 1px rgba(0,0,0,.05);
    }
    .cipanel.collapsed .cipanel-content {
        display: none;
    }
    .cipanel.collapsed .fa.fa-chevron-up:before {
        content: "\f078";
    }
    .cipanel.collapsed .fa.fa-chevron-down:before {
        content: "\f077";
    }
    .cipanel:after,
    .cipanel:before {
        display: table;
    }
    .cipanel-title {
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background-color: #ffffff;
        border-color: #e7eaec;
        border-image: none;
        border-width: 2px 0 0;
        color: inherit;
        margin-bottom: 0;
        padding: 15px 15px 7px;
        min-height: 48px;
    }
    .cipanel-content {
        background-color: #ffffff;
        color: inherit;
        padding: 15px 20px 20px 20px;
        border-color: #e7eaec;
        border-image: none;
        border-style: solid solid none;
        border-width: 1px 0;
    }
    .cipanel-footer {
        color: inherit;
        border-top: 1px solid #e7eaec;
        font-size: 90%;
        background: #ffffff;
        padding: 10px 15px;
    }
    .cipanel-content {
        clear: both;
    }
    .cipanel-heading {
        background-color: #f3f6fb;
        border-bottom: none;
    }
    .cipanel-heading h3 {
        font-weight: 200;
        font-size: 24px;
    }
    .cipanel-title h5 {
        display: inline-block;
        font-size: 14px;
        margin: 0 0 7px;
        padding: 0;
        text-overflow: ellipsis;
        float: left;
    }
    .cipanel-title .label {
        float: left;
        margin-left: 4px;
    }
    .cipanel-tools {
        display: block;
        float: none;
        margin-top: 0;
        position: relative;
        padding: 0;
        text-align: right;
    }
    .cipanel-tools a {
        cursor: pointer;
        margin-left: 5px;
        color: #c4c4c4;
    }
    .cipanel-tools a.btn-primary {
        color: #fff;
    }
    .cipanel-tools .dropdown-menu > li > a {
        padding: 4px 10px;
        font-size: 12px;
    }
    .cipanel .cipanel-tools.open > .dropdown-menu {
        left: auto;
        right: 0;
    }
    .cipanel a.panel-back {
        float: left;
        color: #ffffff;
        background-color: #317589;
    }

    @media(max-width: 991px) {
        .cipanel table a.btn-xs {
            display: block;
            margin-bottom: 4px;
        }
    }

</style>