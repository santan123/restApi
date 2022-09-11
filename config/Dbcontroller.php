<?php
require_once "Dbconnector.php";
class Dbcontroller extends Dbconnector
{
    private $preventData;
    public $course_name;
    public $course_code;
    public $course_unit;

    public function preventor($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $this->preventData = $data;
        return $this->preventData;
    }

    public function adminLogin($data)
    {
        $email = $this->preventor($data['email']);
        $password = $this->preventor($data['password']);
        $query = "SELECT * FROM admin WHERE email=:email";
        $stmt = $this->con->prepare($query);
        $stmt->bindValue(":email", $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $admin_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $admin_info['password'])) {
                $_SESSION['userId'] = $admin_info['id'];
                if (!empty($data['remember'])) {
                    setcookie("userId", $admin_info['id'], time() + 8600, '/');
                }
                return json_encode(
                    [
                        'status' => true,
                        'message' => 'Admin Logged In'
                    ]
                );
            } else {
                return json_encode(
                    [
                        'status' => false,
                        'message' => 'Unable to Log in'
                    ]
                );
            }
        } else {
            return json_encode(
                [
                    'status' => false,
                    'message' => 'Wrong Email'
                ]
            );
        }
    }


    public function select_with_one_parameter($table, $parameter, $value)
    {
        $value = $this->preventor($value);
        $parameter = $this->preventor($parameter);
        $query = "SELECT * FROM $table WHERE $parameter=:parameter";
        $stmt = $this->con->prepare($query);
        $stmt->bindValue(":parameter", $value);
        $sql = $stmt->execute();
        if ($sql) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        } else {
            return false;
        }
    }

    public function select_with_two_parameter($table, $param1, $value1, $param2, $value2)
    {
        $table = $this->preventor($table);
        $param1 = $this->preventor($param1);
        $value1 = $this->preventor($value1);
        $param2 = $this->preventor($param2);
        $value2 = $this->preventor($value2);
        $query = "SELECT * FROM $table WHERE $param1 = :value1 AND $param2 != :value2";
        $stmt       =   $this->con->prepare($query);
        $stmt->bindValue(":value1", $value1);
        $stmt->bindValue(":value2", $value2);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function select_all($table)
    {
        $query = "SELECT * FROM $table";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetchall();
            return $data;
        } else {
            return false;
        }
    }



    public function select_two_tables($table, $table2, $param, $param2)
    {
        $table = $this->preventor($table);
        $table2 = $this->preventor($table2);
        $param = $this->preventor($param);
        $param2 = $this->preventor($param2);
        $query = "SELECT * FROM $table u JOIN $table2 v ON u.$param = v.$param2";
        $stmt =   $this->con->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetchAll();
            return $data;
        } else {
            return false;
        }
    }

    public function select_multipe_row_one_parameter($table, $parameter, $value)
    {
        $query = "SELECT * FROM $table WHERE $parameter=:parameter";
        $stmt = $this->con->prepare($query);
        $stmt->bindValue(":parameter", $value);
        $sql = $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetchall();
            return $data;
        } else {
            return false;
        }
    }

    public function delete_with_one_parameter($table, $param, $value)
    {
        $table = $this->preventor($table);
        $param = $this->preventor($param);
        $value = $this->preventor($value);
        $query = "DELETE FROM $table WHERE $param=:parameter";
        $stmt = $this->con->prepare($query);
        $stmt->bindValue(":parameter", $value);
        $sql = $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $msg =
                [
                    'status' => true,
                    'message' => 'Deleted Successfully'
                ];
            return json_encode($msg);
            exit();
        } else {
            $msg =
                [
                    'status' => false,
                    'message' => 'Unable to Delete'
                ];
            return json_encode($msg);
            exit();
        }
    }

    public function addLevel($post)
    {
        $checkLevel = $this->select_with_one_parameter('level', 'student_level', $post['level']);
        if ($checkLevel !== false) {
            $msg = [
                'status' => false,
                'message' => "Level Already registered"
            ];
            echo json_encode($msg);
            exit();
        }
        $level = $this->preventor($post['level']);
        $uniq_id = $this->unique_id();
        $query = "INSERT INTO level(student_level,uniq_id) VALUES(:level,:uniq_id)";
        $stmt = $this->con->prepare($query);
        $stmt->bindValue(":level", $level);
        $stmt->bindValue(":uniq_id", $uniq_id);
        $sql = $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $msg = [
                'status' => true,
                'message' => "Level Registered"
            ];
            echo json_encode($msg);
            exit();
        } else {
            $msg = [
                'status' => false,
                'message' => "Level Unable to Register"
            ];
            echo json_encode($msg);
            exit();
        }
    }


    public function addStudent($post)
    {
        $admissionNo = htmlspecialchars($post['admissionNo']);
        $firstName = $this->preventor($post['fname']);
        $lastName  = $this->preventor($post['lname']);
        $course    = $this->preventor($post['course']);
        $level     = $this->preventor($post['level']);
        $uniq_id = $this->unique_id();
        $check_student = $this->select_with_one_parameter('student', 'admission_no', $admissionNo);
        if ($check_student !== false) {
            $msg = [
                'status' => false,
                'message' => "Student already Exist"
            ];
            echo json_encode($msg);
            exit();
        } else
            $query = "INSERT INTO student(admission_no,first_name,last_name,course_id,level_id,uniq_id) VALUES(:admission_num,:first_name,:last_name,:course_id,:level_id,:uniq_id)";
        $stmt = $this->con->prepare($query);
        $stmt->bindValue(":admission_num", $admissionNo);
        $stmt->bindValue(":first_name", $firstName);
        $stmt->bindValue(":last_name", $lastName);
        $stmt->bindValue(":level_id", $level);
        $stmt->bindValue(":course_id", $course);
        $stmt->bindValue(":uniq_id", $uniq_id);
        $sql = $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $msg = [
                'status' => true,
                'message' => "Student Registered"
            ];
            echo json_encode($msg);
            exit();
        } else {
            $msg = [
                'status' => false,
                'message' => "Student Not Registered"
            ];
            echo json_encode($msg);
            exit();
        }
    }

    public function addCourse($post)
    {
        $check_course = $this->select_with_one_parameter('course', 'code', $post['courseCode']);
        if ($check_course !== false) {
            $msg = [
                'status' => false,
                'message' => "Course Already registered"
            ];
            echo json_encode($msg);
            exit();
        }
        $uniq_id = $this->unique_id();
        $query = "INSERT INTO course(name,code,unit,uniq_id) VALUES(:course_name,:code,:unit,:uniq_id)";
        $stmt = $this->con->prepare($query);
        $stmt->bindValue(":course_name", $post['courseName']);
        $stmt->bindValue(":code", $post['courseCode']);
        $stmt->bindValue(":unit", $post['courseUnit']);
        $stmt->bindValue(":uniq_id", $uniq_id);
        $sql = $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $msg = [
                'status' => true,
                'message' => "Course registered"
            ];
            echo json_encode($msg);
            exit();
        } else {
            $msg = [
                'status' => false,
                'message' => "Course Unable to Register"
            ];
            echo json_encode($msg);
            exit();
        }
    }
    public function rowCount($table)
    {
        $table = $this->preventor($table);
        $query = "SELECT * FROM $table";
        $stmt  = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->rowCount();
        return $row;
    }


    public function updateCourse($post)
    {
        $courseName = ($post['courseName']);
        $courseCode = $post['courseCode'];
        $courseUnit = $post['courseUnit'];
        $id         = $post['id'];
        $checkUpdatedCourse = $this->select_with_two_parameter("course", "code", $courseCode, "uniq_id", $id);
        if ($checkUpdatedCourse != false) {
            $msg =
                [
                    "status" => false,
                    "message" => "Course Exist"
                ];
            echo json_encode($msg);
            exit();
        } else {
            $query = "UPDATE course SET name=:name,code=:code,unit=:unit WHERE uniq_id = :id ";
            $stmt = $this->con->prepare($query);
            $stmt->bindValue(":name", $courseName);
            $stmt->bindValue(":code", $courseCode);
            $stmt->bindValue(":unit", $courseUnit);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $msg =
                    [
                        'status' => true,
                        'message' => 'Updated Successfully'
                    ];
                echo json_encode($msg);
                exit();
            } else {
                $msg =
                    [
                        'status' => false,
                        'message' => 'Unable to Update',
                    ];
                echo json_encode($msg);
                exit();
            }
        }
    }

    public function updateLevel($post)
    {
        $student_level = $post['studentLevel'];
        $uniq_id       = $post['uniq_id'];
        $checkUpdatedLevel = $this->select_with_two_parameter("level", "student_level", $student_level, "uniq_id", $uniq_id);
        if ($checkUpdatedLevel != false) {
            $msg =
                [
                    "status" => false,
                    "message" => "Level Already Exist"
                ];
            echo json_encode($msg);
            exit();
        } else {
            $query = "UPDATE level SET student_level = :studentLevel WHERE uniq_id = :uniq_id";
            $stmt = $this->con->prepare($query);
            $stmt->bindValue(":studentLevel", $student_level);
            $stmt->bindValue(":uniq_id", $uniq_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $msg =
                    [
                        "status" => true,
                        "message" => "Updated Successfully"
                    ];
                echo json_encode($msg);
                exit();
            } else {
                $msg =
                    [
                        "status" => false,
                        "message" => "Unable To Update"
                    ];
                echo json_encode($msg);
                exit();
            }
        }
    }

    public function updateStudent($post)
    {
        $admissionNo = $this->preventor($post['admissionNo']);
        $firstName = $this->preventor($post['fname']);
        $lastName  = $this->preventor($post['lname']);
        $course    = $this->preventor($post['course']);
        $level     = $this->preventor($post['level']);
        $uniq_id   = $post['uniq_id'];
        $checkUpdatedStudent = $this->select_with_two_parameter("student", "admission_no", $admissionNo, "uniq_id", $uniq_id);
        if ($checkUpdatedStudent != false) {
            $msg =
                [
                    "status" => false,
                    "message" => "Student Already Exist"
                ];
            echo json_encode($msg);
            exit();
        } else {
            $query = "UPDATE student SET admission_no = :admissionNo,first_name = :firstName,last_name = :lastName,course_id = :courseId,level_id =:levelId WHERE uniq_id = :uniq_id";
            $stmt = $this->con->prepare($query);
            $stmt->bindValue(":admissionNo", $admissionNo);
            $stmt->bindValue(":firstName", $firstName);
            $stmt->bindValue(":lastName", $lastName);
            $stmt->bindValue(":courseId", $course);
            $stmt->bindValue(":levelId", $level);
            $stmt->bindValue(":uniq_id", $uniq_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $msg =
                    [
                        "status" => true,
                        "message" => "Updated Successfully"
                    ];
                echo json_encode($msg);
                exit();
            } else {
                $msg =
                    [
                        "status" => false,
                        "message" => "Unable to Update"
                    ];
                echo json_encode($msg);
                exit();
            }
        }
    }

    public function unique_id()
    {
        $id = md5(uniqid() . time());
        return $id;
    }
}//End of the class.
