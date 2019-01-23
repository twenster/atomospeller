<?php
$processed = array();
$graphList = array();

/*
    A directed acyclic graph that stores all possible elemental
    spellings of a word.
*/

class Graph {
    public $nodes = array();
    public $parents_of = array("None" => array());
    public $children_of = array("None" => array());

    public function firsts() {
        // Return nodes with no parents.
        return $this->children_of["None"];
    }
    public function lasts() {
        // Return nodes with no children.
        return $this->parents_of["None"];
    }
    public function add_node($remaining, $position) {
        $node_key = $this->node_in_array($remaining, $position);
        if (!$node_key) {
            $this->nodes[] = array($remaining, $position);
            $t = end($this->nodes);
            $node_key = key($this->nodes);
        }
        return $node_key;
    }

    public function add_edge($parent, $child) {
        // Add a parent-child relatonship to the graph. None is ok as a
        // key, but not a value.

        //if ($parent != "None") {
            $this->parents_of[$child][] = $parent;
        //}
        //if ($child != "None") {
            $this->children_of[$parent][] = $child;
        //}
    }
    public function nodes($connected_only = True) {
        // Return a list of all nodes.
        if ($connected_only) {
            /*return set(
                node for node in
                set($this->_children_of.keys()) | set($this->_parents_of.keys())
                if node is not None
            );*/
            $node = (array_unique(array_merge($this->children_of, $this->parents_of)));
            return $node;
        } else {
            /*return set(
                node for node in
                set($this->_children_of.keys())
                | set($this->_parents_of.keys())
                | set(v for s in $this->_children_of.values() for v in s)
                | set(v for s in $this->_parents_of.values() for v in s)
                if node is not None
            );*/

            $node = (array_unique(array_merge($this->children_of, $this->parents_of)));
            return $node;
        }
    }
    public function edges() {
        // Return a list of all parent-child relationships.
        /*return [
            (parent, child)
            for parent in $this->_children_of
            for child in $this->_children_of[parent]
        ];*/

        $edges = array();
        foreach ($this->children_of[parent] as $child) {
            foreach ($this->children_of as $parent) {
                array_push($edges, [$parent, $child]);
            }
        }
        return $edges;
    }
    public function export($connected_only = True) {
        /* Print a string to stdout that can be piped to Graphviz to
        generate a graph diagram.
        */
        echo 'digraph G {\n';
        echo '    graph [rankdir=LR];\n';
        echo '    node [width=0.75 shape=circle];\n';

        /*$edges = [
            (p, c)
            for p, c in $this->edges()
            if p is not None and c is not None
        ]*/

        $edges = array();
        print_r($this->edges());
        /*foreach ($this->edges() as p => c) {
            if ( ( p != "None") && (c != "None") ) {
                array_push($edges, [p,c]);
            }
        }

        $export = "";
        foreach ($edges as $parent => $child) {
            $export = $export + '    "$parent" -> "$child";\n';
        }
        foreach ($this->nodes($connected_only) as $node) {
            $export = $export + '    "$node" [label="$node.value.capitalize()"];\n';
        }
        $export = $export + '}';*/
        return $export;
    }

    private function node_in_array($remaining, $position) {
        foreach($this->nodes as $key => $item) {
            if ( $item == array($remaining, $position) ) {
                return $key;
            }
        }
        return false;
    }

}

/*
 Displays spellings with elements

$spelled_words = array(
    "word 1" = array( // list of suggestions / paths
        0 => array( // suggestion 0 = path 0
            "elemental_word" =>
            "elements" => array(
                symbol =>
                number =>
                url =>
            )
        )
        1 => array( // suggestion 1 = path 1
            ...
        )
    )
    "word 2" => array( // list of suggestions / paths
        ...
    )
)
*/

function doSpell($wordQuery) {
    global $processed, $graphList;
    $words = preg_replace('/[\W]+/', '', explode(' ', $wordQuery));

    /*
        Builds an array of all possible spelling for each queried word
    */
    $spellingList = array();
    foreach ($words as $word_key => $word) {
        $processed = array();
        $graphList[$word_key] = new Graph;
        $spellingList[$word_key] = buildGraph($word_key, $word);
    }

    /*
        Builds Array and convert into JSON
    */
    $spelled_words = array();
    $spelled_words["atomo_settings"] = array();
    $spelled_words["atomo_settings"]["source_element_width"] = SOURCEELEMENTWIDTH;
    //$spelled_words["atomo_settings"]["country_code"] = getCountry();
    $spelled_words["query_components"] = array();

    foreach ($spellingList as $word_key => $spelling) {

        $spelled_words["query_components"][ $words[$word_key] ] = array();
        $spelled_words["query_components"][ $words[$word_key] ]["shortest"] = array(); // shortest paths
        $spelled_words["query_components"][ $words[$word_key] ]["all"] = array(); // list of all paths
        $shortest_length = strlen($words[$word_key]);

        foreach ($spelling as $path_id => $paths) {
            $spelled_words["query_components"][ $words[$word_key] ]["all"][$path_id]                    = array(); // path #x
            $spelled_words["query_components"][ $words[$word_key] ]["all"][$path_id]["symbolized_word"] = ""; // elements imploded
            $spelled_words["query_components"][ $words[$word_key] ]["all"][$path_id]["symbols"]         = array(); // elements in array

            foreach ($paths as $node) {
                $spelled_words["query_components"][ $words[$word_key] ]["all"][$path_id]["symbolized_word"] = $spelled_words["query_components"][ $words[$word_key] ]["all"][$path_id]["symbolized_word"] . ucfirst($graphList[$word_key]->nodes[$node][0]);
                $spelled_words["query_components"][ $words[$word_key] ]["all"][$path_id]["symbols"][]       = ucfirst($graphList[$word_key]->nodes[$node][0]);
            }

            // saving the shortest word found
            if (count($paths) <= $shortest_length) {
                $shortest_length = count($paths);
                $spelled_words["query_components"][ $words[$word_key] ]["shortest"][0] = $spelled_words["query_components"][ $words[$word_key] ]["all"][$path_id];
            }

        }

        // save each words for statistics
        setLog($words[$word_key]);
    }
    return json_encode($spelled_words);
}

function displaySpelledWords($spelled_words_json) {
    header('Content-Type: application/json');
    echo $spelled_words_json;
}

/*
    Return a list of any possible ways to spell a word with a given set of symbols.
    Example:
    >>> spell('amputation')
    array (
        ['Am', 'Pu', 'Ta', 'Ti', 'O', 'N'],
        ['Am', 'P', 'U', 'Ta', 'Ti', 'O', 'N']
    );
*/

function buildGraph($word_key, $word) {
    global $graphList;

    /*
        Builds an array (graph) of possible element and connection between them
        to build the requested word.
    */
    popRoot($word_key, $word);

    /*
        Builds an array of array of all nodes (path) built to the requested word
    */
    $elemental_spellings = array();
    foreach ($graphList[$word_key]->firsts() as $first) {
        foreach ($graphList[$word_key]->lasts() as $last) {
            foreach (findAllPaths($graphList[$word_key]->children_of, $first, $last) as $path) {

                array_push($elemental_spellings, $path);
            }
        }
    }

    return $elemental_spellings;
}

/*
    Pop the single and double-character roots off the front of a
    given string, then recurse into what remains.
    For the word 'because', the roots and remainders for each call
    look something like:
        'b' 'ecause'
            'e' 'cause'
                'c' 'ause'
                    'a' 'use'
                        'u' 'se'
                            's' 'e'
                                'e' ''
                            'se' ''
                        'us' 'e'
                    'au' 'se'
                'ca' 'use'
            'ec' 'ause'
        'be' 'cause'
    For each root present in the set of allowed symbols, add an edge
    to the graph:
        previous root --> current root.
    Keep track of processed values for `remaining` and do not
    evaluate a given value more than once.
    Keep track of the position of each root so that repeated roots
    are distinct nodes.
*/

function popRoot($word_key, $remaining, $position = 0, $previous_root = "None") {
    global $processed, $graphList;
    $dbSymbolList = getSymbolsFromDB();
    if ($remaining == '') {
        $graphList[$word_key]->add_edge($previous_root, "None");
        return;
    }

    if (in_array(ucfirst($remaining[0]), $dbSymbolList)) {
        $single_root = $graphList[$word_key]->add_node($remaining[0], $position);
        $graphList[$word_key]->add_edge($previous_root, $single_root);
        if (!in_array($remaining, $processed)) {
            popRoot($word_key, substr($remaining, 1), $position + 1, $single_root);
        }
    }
    if (strlen($remaining) >= 2) {
        if (in_array(ucfirst(substr($remaining, 0, 2)), $dbSymbolList)) {
            $double_root = $graphList[$word_key]->add_node(substr($remaining, 0, 2), $position);
            $graphList[$word_key]->add_edge($previous_root, $double_root);
            if (!in_array($remaining, $processed)) {
                popRoot($word_key, substr($remaining, 2), $position + 2, $double_root);
            }
        }
    }
    $processed[] = $remaining;
}

function findAllPaths($parents_to_children, $start, $end, $path=[]) {
    /* Return a list of all paths through a graph from start to end.
    `parents_to_children` is a dict with parent nodes as keys and sets
    of child nodes as values.
    Based on https://www.python.org/doc/essays/graphs/
    */
    $path[] = $start;
    if ($start == $end) {
        return [$path];
    }
    if (!$parents_to_children[$start]) {
        return [];
    }
    $paths = [];
    foreach ($parents_to_children[$start] as $node) {
        if (!in_array($node, $path)) {
            $newpaths = findAllPaths($parents_to_children, $node, $end, $path);
            foreach ($newpaths as $newpath) {
                array_push($paths, (ARRAY) $newpath);
            }
        }
    }
    return $paths;
}
?>
