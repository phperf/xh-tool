# XHPROF profile viewer CLI tool

A command-line tool to analyze XHPROF hierarchical data.

## Installation

Download `xh-tool.phar` from releases, optionally install bash completions and symlink to `/usr/local/bin` with:
```
php xh-tool.phar --install
```

Or install via `composer`:

```
composer require --dev phperf/xh-tool
```

## Usage

Get a serialized profile data file, example: `xhprof_report_sample.1518071438.9016.serialized`.

### Info

Get basic info about profile

```
xh-tool info --help
v1.0.0 xh-tool info
XHPROF profile viewer
Usage: 
   xh-tool info <profile>
   profile   Path to XHPROF hierarchical profile
```

```
xh-tool info xhprof_report_sample.1518071438.9016.serialized 

Nodes: 2082
Functions: 731
Calls: 16M
Total
name     wallTime   wallTime%   wallTime1   ownTime   ownTime%   ownTime1   count
main()   34.99s     100         34.99s      7.56ms    0.02       7.56ms     1    
```

### Top

Get ordered functions list

```
xh-tool top --help
v1.0.0 xh-tool top
XHPROF profile viewer
Usage: 
   xh-tool top <profile>
   profile   Path to XHPROF hierarchical profile
   
Options: 
   --strip-nesting     Strip @N for nested calls                                                                                      
   --limit <limit>     Number of rows in result, default no limit                                                                     
   --filter <filter>   Case-insensitive regex to filter by function name, example: "process$", "swaggest", "^MyNs\\MyClass\\MyMethod$"
   --order <order>     Order by field, default: ownTime                                                                               
                       Allowed values: name, wallTime, wallTime1, wallTime%, ownTime, ownTime1, ownTime%, count
```

Columns:
`wallTime` is time spent in function (including child function calls).
`wallTime%` is time spent in function normalized to `main()` total time.
`wallTime1` is average time spent in 1 function call, valued as `wallTime / count`.
`ownTime` is time spent in function (excluding child function calls).
`ownTime%` is `ownTime` normalized to to `main()` total time.
`ownTime1` is average time spent in 1 function call (excluding child function calls), valued as `ownTime / count`.
`count` is number of function calls happened.


```
xh-tool top xhprof_report_sample.1518071438.9016.serialized --order ownTime --limit 10 --filter swaggest

name                                                                     wallTime   wallTime%   wallTime1   ownTime    ownTime%   ownTime1   count
Swaggest\JsonSchema\Schema::process                                      12.52s     35.79       113.2us     1.12s      3.2        10.1us     111K 
Swaggest\JsonSchema\Schema::process@1                                    9.2s       26.3        98.1us      1.05s      2.99       11.1us     94K  
Swaggest\JsonSchema\Schema::processObject                                10.05s     28.72       138.6us     965.02ms   2.76       13.3us     73K  
Swaggest\JsonSchema\Tests\PHPUnit\Spec\Draft7OpisTest::runSpecTest       14.78s     42.23       275.6us     884.17ms   2.53       16.5us     54K  
Swaggest\JsonSchema\Schema::in                                           14.36s     41.04       129.8us     813.56ms   2.33       7.4us      111K 
Swaggest\JsonSchema\Schema::process@2                                    6.98s      19.95       101us       773.07ms   2.21       11.2us     69K  
Swaggest\JsonSchema\Schema::process@3                                    4.99s      14.26       81.4us      740.24ms   2.12       12.1us     61K  
Swaggest\JsonSchema\Tests\PHPUnit\Spec\Draft7SwaggestTest::runSpecTest   17.01s     48.62       307.6us     734.24ms   2.1        13.3us     55K  
Swaggest\JsonSchema\Constraint\Type::isValid                             746.13ms   2.13        2.8us       718.75ms   2.05       2.7us      269K 
Swaggest\JsonSchema\Schema::process@4                                    3.06s      8.76        54.2us      691.08ms   1.98       12.2us     57K  
```

### Func

Get info on specific function

```
xh-tool func --help
v1.0.0 xh-tool func
XHPROF profile viewer
Usage: 
   xh-tool func <profile> <filter>
   profile   Path to XHPROF hierarchical profile                                                                            
   filter    Case-insensitive regex to filter by function name, example: "process$", "swaggest", "^MyNs\\MyClass\\MyMethod$"
   
Options: 
   --strip-nesting    Strip @N for nested calls                                                               
   --limit <limit>    Number of rows in result, default no limit                                              
   --order <order>    Order by field, default: ownTime                                                        
                      Allowed values: name, wallTime, wallTime1, wallTime%, ownTime, ownTime1, ownTime%, count
```

```
xh-tool func xhprof_report_sample.1518071438.9016.serialized Schema::process$

Function
name                                  wallTime   wallTime%   wallTime1   ownTime   ownTime%   ownTime1   count
Swaggest\JsonSchema\Schema::process   12.52s     35.79       113.2us     1.12s     3.2        10.1us     111K 

Parents
name                              wallTime   wallTime%   wallTime1   count
Swaggest\JsonSchema\Schema::in    12.42s     35.51       112.3us     111K 
Swaggest\JsonSchema\Schema::out   97.63ms    0.28        97.63ms     1    

Children
name                                         wallTime   wallTime%   wallTime1   count
Swaggest\JsonSchema\Schema::processType      308.63ms   0.88        4.8us       65K  
Swaggest\JsonSchema\Schema::processAnyOf     66.77ms    0.19        51.4us      1K   
SplObjectStorage::attach                     2us        0           2us         1    
SplObjectStorage::contains                   1us        0           1us         1    
Swaggest\JsonSchema\Schema::processContent   17.4ms     0.05        24.9us      700  
Swaggest\JsonSchema\Schema::processOneOf     102.59ms   0.29        73.3us      1K   
Swaggest\JsonSchema\Schema::processIf        40.04ms    0.11        33.4us      1K   
Swaggest\JsonSchema\Schema::processEnum      2.68ms     0.01        5.4us       500  
Swaggest\JsonSchema\Schema::processConst     48.98ms    0.14        40.8us      1K   
Swaggest\JsonSchema\Schema::processNot       51.65ms    0.15        25.8us      2K   
Swaggest\JsonSchema\Schema::processAllOf     94.92ms    0.27        59.3us      2K   
array_key_exists                             12.17ms    0.03        0.1us       105K 
Swaggest\JsonSchema\Schema::processNumeric   14.43ms    0.04        1.9us       8K   
Swaggest\JsonSchema\Schema::processArray     212.15ms   0.61        30.3us      7K   
Swaggest\JsonSchema\Schema::processString    431.46ms   1.23        25.1us      17K  
is_array                                     5.39ms     0.02        0.1us       88K  
Swaggest\JsonSchema\Schema::processObject    9.97s      28.5        143.9us     69K  
is_float                                     7.24ms     0.02        0.1us       93K  
is_int                                       7.64ms     0.02        0.1us       97K  
is_string                                    8.06ms     0.02        0.1us       103K 
PHPUnitBenchmark\Suite::jsonSerialize        22us       0           22us        1    
```