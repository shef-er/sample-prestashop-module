<?php

/**
 * Class Students
 */

class Students extends ObjectModel
{
    public $id;

    /** @var int student ID */
    public $id_student;

    /** @var mixed string or array of Name */
    public $name;

    /** @var bool Status for display */
    public $active = 1;

    /** @var string Object creation date */
    public $date_born;

    /** @var float student average grade */
    public $average_grade;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'students',
        'primary' => 'id_student',
        'multilang' => true,
        'fields' => array(
            'id_student'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'average_grade'     => array('type' => self::TYPE_FLOAT),
            'active'            => array('type' => self::TYPE_BOOL),
            'date_born'         => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            // Language fields
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
        ),
    );

    /**
     * Get All Students
     *
     * @return array|null
     */
    public function getAllStudents()
    {
        $cacheId = 'Students::getAllStudents_'.(int) $this->id;
        if (!Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->select('s.*');
            $sql->from('students', 's');
            $result = Db::getInstance()->executeS($sql);
            $students = array();
            foreach ($result as $row) {
                $student = array();
                $student['id_student'] = $row['id_student'];
                $student['average_grade'] = $row['average_grade'];
                $student['active'] = $row['active'];
                $student['date_born'] = $row['date_born'];
                $student['name'] = $row['name'];
                $students[] = $student;
            }
            Cache::store($cacheId, $students);

            return $students;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get Best Student by average_grade
     *
     * @return array|null
     */
    public function getBestStudent()
    {
        $cacheId = 'Students::getBestStudent_'.(int) $this->id;
        if (!Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->select('s.*, MAX(s.`average_grade`) AS `average_grade`');
            $sql->from('students', 's');
            $result = Db::getInstance()->executeS($sql);
            $student = array();
            foreach ($result as $row) {
                $student['id_student'] = $row['id_student'];
                $student['average_grade'] = $row['average_grade'];
                $student['active'] = $row['active'];
                $student['date_born'] = $row['date_born'];
                $student['name'] = $row['name'];
            }
            Cache::store($cacheId, $student);

            return $student;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get Best Average Grade
     *
     * @return array|null
     */
    public function getBestAverageGrade()
    {
        $cacheId = 'Students::getBestAverageGrade_'.(int) $this->id;
        if (!Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->select('MAX(s.`average_grade`)');
            $sql->from('students', 's');
            $result = Db::getInstance()->executeS($sql);
            $grades = array();
            foreach ($result as $row) {
                $grades[] = $row[0];
            }

            Cache::store($cacheId, $grades[0]);

            return $grades[0];
        }

        return Cache::retrieve($cacheId);
    }


}
