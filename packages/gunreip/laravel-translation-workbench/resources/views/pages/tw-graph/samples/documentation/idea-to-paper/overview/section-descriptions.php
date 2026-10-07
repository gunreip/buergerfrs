<?php

/**
 * Short sub-headers for the Overview source accordions.
 * data/structure keys match the corresponding filename without its extension.
 * Remove an entry or set it to '' to omit its text; no fallback text is rendered.
 * Keep layout overrides in data/*.php; this file contains explanations only.
 */
return [
    'layout-defaults' => __('These are the shared layout defaults used throughout the overview.'),
    'subtree-renderer' => __('This is the shared subtree renderer used throughout the overview.'),
    'structure-data' => __('This is the structure data class used in the overview.'),

    'data' => [
        'canvas' => __('Canvas dimensions, padding and the layout of its example branches.'),
        'deep-reference' => __('Layout settings for Strang and Parts and their component reference entries.'),
        'flow' => __('Layout settings for control-flow topics and their nested example groups.'),
        'inventory' => __('Caption and connection settings for the Inventory endpoint.'),
        'main-tabs' => __('Root caption, canvas dimensions and shared connections between the main tabs.'),
        'overview' => __('Caption and connection settings for the Overview endpoint.'),
        'parts' => __('Layout settings for the Parts examples and their branches.'),
        'paths' => __('Layout settings for the Paths examples and their branches.'),
        'primitives' => __('Layout settings for primitive examples, markers and connectors.'),
        'segments' => __('Layout settings for segment examples and their branches.'),
        'strang-branch' => __('Layout settings for the Strang Branch variants and their example groups.'),
        'strang-merge' => __('Layout settings for the Strang Merge variants and their example groups.'),
        'strang-rekey' => __('Layout settings for the Strang Rekey variants and their example groups.'),
        'strang-trunk' => __('Layout settings for the Strang Trunk variants and their example groups.'),
    ],

    'structure' => [
        'orchestrator' => __('Loads the layout overrides, creates the canvas and includes the rendering sections.'),
        'root' => __('Renders the Idea to Paper start label and the shared entry anchor.'),
        'canvas' => __('Renders the Canvas sub-tabs and their nested prop examples.'),
        'deep-reference' => __('Renders Strang and Parts as sibling branches with their public component references.'),
        'flow' => __('Renders the Flow topics and their example groups through the shared tree renderer.'),
        'inventory' => __('Reserved for future Inventory sub-tabs; the main tab currently ends at its caption.'),
        'main-tabs' => __('Connects the main tabs through the left and right merge chains.'),
        'overview' => __('Reserved for future Overview sub-tabs; the main tab currently ends at its caption.'),
        'parts' => __('Renders the Parts sub-tabs through the shared tree renderer.'),
        'paths' => __('Renders the Paths sub-tabs through the shared tree renderer.'),
        'primitives' => __('Renders the Primitives sub-tabs and the nested marker and connector examples.'),
        'segments' => __('Renders the Segments sub-tabs through the shared tree renderer.'),
        'strang-branch' => __('Renders the Strang Branch sub-tabs and their nested examples.'),
        'strang-merge' => __('Renders the Strang Merge sub-tabs and their nested examples.'),
        'strang-rekey' => __('Renders the Strang Rekey sub-tabs and their nested examples.'),
        'strang-trunk' => __('Renders the Strang Trunk sub-tabs and their nested examples.'),
    ],
];
