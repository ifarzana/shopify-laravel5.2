<style>
    /*PAGINATION*/
    #pagination {
        height: 36px;
        margin: 18px 0;
        display: block !important;
        text-align: center;
    }
    .pagination {
        display: inline-block;
        margin: 0;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }

    .pagination li {
        display: inline;
    }

    .pagination > li > a,
    .pagination > li > span {
        margin-left: auto;
        color: #317589;
    }

    .pagination > .active > a,
    .pagination > .active > span,
    .pagination > .active > a:hover,
    .pagination > .active > span:hover,
    .pagination > .active > a:focus,
    .pagination > .active > span:focus {
        background-color: #317589;
        border-color: #317589;
        cursor: default;
        pointer-events: none;
        color: #ffffff;

    }

    .pagination > li > a:hover,
    .pagination > li > span:hover,
    .pagination > li > a:focus,
    .pagination > li > span:focus {
        color: #317589;
    }

    .pagination > li > span {
        padding-left: 14px;
        padding-right: 14px;
        line-height: 22px;
        margin-top: 0;
    }

    .pagination li a {
        float: left;
        padding: 0 14px;
        line-height: 24px;
        text-decoration: none;
        border: 1px solid #ddd;
        border-left-width: 0;
        background-color: transparent;
    }

    .pagination li a:hover,
    .pagination li.active span {
        background-color: #f5f5f5;
    }

    .pagination li.active span {
        color: #999999;
        border-color: #ddd;
        cursor: default;
        pointer-events: none;
        margin-left: -1px;
    }

    .pagination li.disabled span,
    .pagination li.disabled span:hover {
        color: #999999;
        background-color: transparent;
        cursor: default;
        pointer-events: none;
    }

    .pagination li:first-child a {
        border-left-width: 1px;
        -webkit-border-radius: 3px 0 0 3px;
        -moz-border-radius: 3px 0 0 3px;
        border-radius: 3px 0 0 3px;
    }

    .pagination li:last-child a {
        -webkit-border-radius: 0 3px 3px 0;
        -moz-border-radius: 0 3px 3px 0;
        border-radius: 0 3px 3px 0;
    }

    @media(max-width: 767px) {
        .pagination li.prev-li a {
            border-left-width: 1px;
        }
    }

    .pagination-result {
        text-align: left;
    }
    .pagination-result .results-per-page-div {
        float: right;
        margin-top: -4px;
    }

    .pagination-result .results-per-page-div .form-control {
        display: inline-block;
        width: auto;
        height: 26px;
        padding: 0 6px;
    }

    @media (max-width: 460px) {

        .pagination a {
            padding: 0 8px;
        }

    }
</style>