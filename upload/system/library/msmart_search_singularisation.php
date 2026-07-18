<?php

/**
 * @since  1.0
 * @author Konsta Vesterinen
 * @author Jonathan H. Wage
 */
class Msmart_Search_Singularisation
{
    private static $plural = array(
        'rules' => array(
            '/(s)tatus$/i' => '\1\2tatuses',
            '/(quiz)$/i' => '\1zes',
            '/^(ox)$/i' => '\1\2en',
            '/([m|l])ouse$/i' => '\1ice',
            '/(matr|vert|ind)(ix|ex)$/i' => '\1ices',
            '/(x|ch|ss|sh)$/i' => '\1es',
            '/([^aeiouy]|qu)y$/i' => '\1ies',
            '/(hive)$/i' => '\1s',
            '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
            '/sis$/i' => 'ses',
            '/([ti])um$/i' => '\1a',
            '/(p)erson$/i' => '\1eople',
            '/(m)an$/i' => '\1en',
            '/(c)hild$/i' => '\1hildren',
            '/(f)oot$/i' => '\1eet',
            '/(buffal|her|potat|tomat|volcan)o$/i' => '\1\2oes',
            '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
            '/us$/i' => 'uses',
            '/(alias)$/i' => '\1es',
            '/(analys|ax|cris|test|thes)is$/i' => '\1es',
            '/s$/' => 's',
            '/^$/' => '',
            '/$/' => 's',
        ),
        'uninflected' => array(
            '.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', 'people', 'cookie'
        ),
        'irregular' => array(
            'atlas' => 'atlases',
            'axe' => 'axes',
            'beef' => 'beefs',
            'brother' => 'brothers',
            'cafe' => 'cafes',
            'chateau' => 'chateaux',
            'child' => 'children',
            'cookie' => 'cookies',
            'corpus' => 'corpuses',
            'cow' => 'cows',
            'criterion' => 'criteria',
            'curriculum' => 'curricula',
            'demo' => 'demos',
            'domino' => 'dominoes',
            'echo' => 'echoes',
            'foot' => 'feet',
            'fungus' => 'fungi',
            'ganglion' => 'ganglions',
            'genie' => 'genies',
            'genus' => 'genera',
            'graffito' => 'graffiti',
            'hippopotamus' => 'hippopotami',
            'hoof' => 'hoofs',
            'human' => 'humans',
            'iris' => 'irises',
            'leaf' => 'leaves',
            'loaf' => 'loaves',
            'man' => 'men',
            'medium' => 'media',
            'memorandum' => 'memoranda',
            'money' => 'monies',
            'mongoose' => 'mongooses',
            'motto' => 'mottoes',
            'move' => 'moves',
            'mythos' => 'mythoi',
            'niche' => 'niches',
            'nucleus' => 'nuclei',
            'numen' => 'numina',
            'occiput' => 'occiputs',
            'octopus' => 'octopuses',
            'opus' => 'opuses',
            'ox' => 'oxen',
            'penis' => 'penises',
            'person' => 'people',
            'plateau' => 'plateaux',
            'runner-up' => 'runners-up',
            'sex' => 'sexes',
            'soliloquy' => 'soliloquies',
            'son-in-law' => 'sons-in-law',
            'syllabus' => 'syllabi',
            'testis' => 'testes',
            'thief' => 'thieves',
            'tooth' => 'teeth',
            'tornado' => 'tornadoes',
            'trilby' => 'trilbys',
            'turf' => 'turfs',
            'volcano' => 'volcanoes',
        )
    );

    private static $singular = array(
        'rules' => array(
            '/(s)tatuses$/i' => '\1\2tatus',
            '/^(.*)(menu)s$/i' => '\1\2',
            '/(quiz)zes$/i' => '\\1',
            '/(matr)ices$/i' => '\1ix',
            '/(vert|ind)ices$/i' => '\1ex',
            '/^(ox)en/i' => '\1',
            '/(alias)(es)*$/i' => '\1',
            '/(buffal|her|potat|tomat|volcan)oes$/i' => '\1o',
            '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
            '/([ftw]ax)es/i' => '\1',
            '/(analys|ax|cris|test|thes)es$/i' => '\1is',
            '/(shoe|slave)s$/i' => '\1',
            '/(o)es$/i' => '\1',
            '/ouses$/' => 'ouse',
            '/([^a])uses$/' => '\1us',
            '/([m|l])ice$/i' => '\1ouse',
            '/(x|ch|ss|sh)es$/i' => '\1',
            '/(m)ovies$/i' => '\1\2ovie',
            '/(s)eries$/i' => '\1\2eries',
            '/([^aeiouy]|qu)ies$/i' => '\1y',
            '/([lr])ves$/i' => '\1f',
            '/(tive)s$/i' => '\1',
            '/(hive)s$/i' => '\1',
            '/(drive)s$/i' => '\1',
            '/([^fo])ves$/i' => '\1fe',
            '/(^analy)ses$/i' => '\1sis',
            '/(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
            '/([ti])a$/i' => '\1um',
            '/(p)eople$/i' => '\1\2erson',
            '/(m)en$/i' => '\1an',
            '/(c)hildren$/i' => '\1\2hild',
            '/(f)eet$/i' => '\1oot',
            '/(n)ews$/i' => '\1\2ews',
            '/eaus$/' => 'eau',
            '/^(.*us)$/' => '\\1',
            '/s$/i' => '',
        ),
        'uninflected' => array(
            '.*[nrlm]ese',
            '.*deer',
            '.*fish',
            '.*measles',
            '.*ois',
            '.*pox',
            '.*sheep',
            '.*ss',
        ),
        'irregular' => array(
            'criteria'  => 'criterion',
            'curves'    => 'curve',
            'emphases'  => 'emphasis',
            'foes'      => 'foe',
            'hoaxes'    => 'hoax',
            'media'     => 'medium',
            'neuroses'  => 'neurosis',
            'waves'     => 'wave',
            'oases'     => 'oasis',
        )
    );

    private static $uninflected = array(
        'Amoyese', 'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus',
        'carp', 'chassis', 'clippers', 'cod', 'coitus', 'Congoese', 'contretemps', 'corps',
        'debris', 'diabetes', 'djinn', 'eland', 'elk', 'equipment', 'Faroese', 'flounder',
        'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
        'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings',
        'jackanapes', 'Kiplingese', 'Kongoese', 'Lucchese', 'mackerel', 'Maltese', '.*?media',
        'mews', 'moose', 'mumps', 'Nankingese', 'news', 'nexus', 'Niasese',
        'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese',
        'proceedings', 'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors',
        'sea[- ]bass', 'series', 'Shavese', 'shears', 'siemens', 'species', 'staff', 'swine',
        'testes', 'trousers', 'trout', 'tuna', 'Vermontese', 'Wenchowese', 'whiting',
        'wildebeest', 'Yengeese'
    );

    private static $cache = array();

    public static function pluralize($word)
    {
        if (isset(self::$cache['pluralize'][$word])) {
            return self::$cache['pluralize'][$word];
        }

        if (!isset(self::$plural['merged']['irregular'])) {
            self::$plural['merged']['irregular'] = self::$plural['irregular'];
        }

        if (!isset(self::$plural['merged']['uninflected'])) {
            self::$plural['merged']['uninflected'] = array_merge(self::$plural['uninflected'], self::$uninflected);
        }

        if (!isset(self::$plural['cacheUninflected']) || !isset(self::$plural['cacheIrregular'])) {
            self::$plural['cacheUninflected'] = '(?:' . implode('|', self::$plural['merged']['uninflected']) . ')';
            self::$plural['cacheIrregular']   = '(?:' . implode('|', array_keys(self::$plural['merged']['irregular'])) . ')';
        }

        if (preg_match('/(.*)\\b(' . self::$plural['cacheIrregular'] . ')$/i', $word, $regs)) {
            self::$cache['pluralize'][$word] = $regs[1] . substr($word, 0, 1) . substr(self::$plural['merged']['irregular'][strtolower($regs[2])], 1);
            
            return self::$cache['pluralize'][$word];
        }

        if (preg_match('/^(' . self::$plural['cacheUninflected'] . ')$/i', $word, $regs)) {
            self::$cache['pluralize'][$word] = $word;

            return $word;
        }

        foreach (self::$plural['rules'] as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                self::$cache['pluralize'][$word] = preg_replace($rule, $replacement, $word);

                return self::$cache['pluralize'][$word];
            }
        }
    }

    public static function singularize($word)
    {
        if (isset(self::$cache['singularize'][$word])) {
            return self::$cache['singularize'][$word];
        }

        if (!isset(self::$singular['merged']['uninflected'])) {
            self::$singular['merged']['uninflected'] = array_merge(
                self::$singular['uninflected'],
                self::$uninflected
            );
        }

        if (!isset(self::$singular['merged']['irregular'])) {
            self::$singular['merged']['irregular'] = array_merge(
                self::$singular['irregular'],
                array_flip(self::$plural['irregular'])
            );
        }

        if (!isset(self::$singular['cacheUninflected']) || !isset(self::$singular['cacheIrregular'])) {
            self::$singular['cacheUninflected'] = '(?:' . join('|', self::$singular['merged']['uninflected']) . ')';
            self::$singular['cacheIrregular'] = '(?:' . join('|', array_keys(self::$singular['merged']['irregular'])) . ')';
        }

        if (preg_match('/(.*)\\b(' . self::$singular['cacheIrregular'] . ')$/i', $word, $regs)) {
            self::$cache['singularize'][$word] = $regs[1] . substr($word, 0, 1) . substr(self::$singular['merged']['irregular'][strtolower($regs[2])], 1);
            
            return self::$cache['singularize'][$word];
        }

        if (preg_match('/^(' . self::$singular['cacheUninflected'] . ')$/i', $word, $regs)) {
            self::$cache['singularize'][$word] = $word;

            return $word;
        }

        foreach (self::$singular['rules'] as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                self::$cache['singularize'][$word] = preg_replace($rule, $replacement, $word);

                return self::$cache['singularize'][$word];
            }
        }

        self::$cache['singularize'][$word] = $word;

        return $word;
    }
}
