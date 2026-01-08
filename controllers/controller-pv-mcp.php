<?php

add_action('rest_api_init', 'pv_register_mcp_routes');
function pv_register_mcp_routes()
{
    $mcp = new PV_MCP();
    $mcp->register_routes();
}
