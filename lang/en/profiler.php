<?php

return [
    'typologies'                       => '++++ Typologies ++++',
    'run'                              => 'Run',
    'typology'                         => 'Typology',
    'profiling'                        => '++++ Query Profiling ++++',
    'time'                             => 'Time (ms)',
    'query'                            => 'Query',
    'limit_rows'                       => 'Show :LIMIT_ROWS of :TOTAL_ROWS',
    'more_rows'                        => 'To console more rows please change the limit on the db profiler config file',
    'total_time'                       => 'Total Time (ms)',
    'duplicate'                        => 'Duplicate',
    'n1_problem'                       => '(Maybe N+1 Problem?)',
    'hints'                            => '++++ Hints ++++',
    'select_has_an_asterisk'           => 'Use <error>SELECT *</> only if you need all columns from table',
    'order_by_random'                  => '<error>ORDER BY RAND()</> is slow, try to avoid if you can.',
    'wrong_not_equal_operator_exists'  => 'The <error>!=</> operator is not standard. Use the '
        . '<error><></> operator to test for inequality instead.',
    'where_clause_not_exists'          => 'The <error>SELECT</> statement has no '
        . '<error>WHERE</> clause and could examine many more rows than intended',
    'limit_without_order_by'           => '<error>LIMIT</> without '
        . '<error>ORDER BY</> causes non-deterministic results, depending on the query execution plan',
    'like_clause_with_prefix_wildcard' => 'An argument has a leading wildcard character: '
        . '<error>:MATCH</>.The predicate with this argument is not sargable and cannot use an index if one exists.',
];
