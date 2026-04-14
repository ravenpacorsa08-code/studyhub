-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2026 at 04:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studyhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'Introduction to PHP', 'Learn the basics of PHP programming from scratch.', '2026-04-11 01:55:01'),
(2, 'MySQL for Beginners', 'Master database design and SQL queries.', '2026-04-11 01:55:01'),
(3, 'Java Programming', 'Learn object-oriented programming with Java — from basics to OOP concepts, arrays, and exception handling.', '2026-04-11 12:40:22'),
(4, 'Python Programming', 'Master Python from the ground up — variables, data structures, functions, file handling, and more.', '2026-04-11 12:40:22'),
(5, 'C++ Programming', 'Dive into C++ — a powerful language for systems and game development, covering pointers, classes, and STL.', '2026-04-11 12:40:22');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` longtext DEFAULT NULL,
  `position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `title`, `content`, `position`) VALUES
(1, 1, 'What is PHP?', '<p>PHP (Hypertext Preprocessor) is a popular server-side scripting language used for web development. It runs on the server and generates HTML that is sent to the browser.</p><p>PHP files end with <code>.php</code> and can contain HTML, CSS, JavaScript, and PHP code together.</p>', 1),
(2, 1, 'Variables and Data Types', '<p>In PHP, variables start with a <code>$</code> sign. PHP is loosely typed — you do not need to declare data types.</p><pre><code>$name = \"StudyHub\";\n$age = 25;\n$price = 99.5;\n$active = true;</code></pre>', 2),
(3, 1, 'Control Structures', '<p>PHP supports <code>if</code>, <code>else</code>, <code>elseif</code>, <code>while</code>, <code>for</code>, and <code>foreach</code> loops just like most languages.</p><pre><code>if ($score >= 75) {\n    echo \"Passed!\";\n} else {\n    echo \"Try again.\";\n}</code></pre>', 3),
(4, 1, 'Functions in PHP', '<p>Functions allow you to reuse code. Use the <code>function</code> keyword to define one.</p><pre><code>function greet($name) {\n    return \"Hello, \" . $name . \"!\";\n}\necho greet(\"Juan\");</code></pre>', 4),
(5, 1, 'Working with Forms', '<p>PHP can collect form data using <code>$_POST</code> and <code>$_GET</code> superglobals.</p><pre><code>if ($_SERVER[\"REQUEST_METHOD\"] == \"POST\") {\n    $name = $_POST[\"name\"];\n    echo \"Welcome, \" . $name;\n}</code></pre>', 5),
(6, 2, 'Introduction to Databases', '<p>A database stores organized data. MySQL is a relational database system that uses tables, rows, and columns.</p>', 1),
(7, 2, 'Basic SQL Queries', '<p>Use <code>SELECT</code> to retrieve data, <code>INSERT</code> to add, <code>UPDATE</code> to modify, and <code>DELETE</code> to remove records.</p><pre><code>SELECT * FROM users WHERE id = 1;</code></pre>', 2),
(8, 2, 'JOIN Operations', '<p>JOINs let you combine rows from two or more tables based on a related column.</p><pre><code>SELECT u.name, c.title\nFROM users u\nJOIN enrollments e ON u.id = e.user_id\nJOIN courses c ON e.course_id = c.id;</code></pre>', 3),
(9, 2, 'Indexes and Optimization', '<p>Indexes speed up SELECT queries. Use them on columns you frequently search or sort by.</p><pre><code>CREATE INDEX idx_email ON users(email);</code></pre>', 4),
(25, 3, 'What is Java?', '<p>Java is a high-level, class-based, object-oriented language (1995, Sun Microsystems). It follows the <strong>Write Once, Run Anywhere</strong> principle via the JVM.</p>', 1),
(26, 3, 'Setting Up Java', '<p>Install the <strong>JDK</strong> from oracle.com. Use an IDE like IntelliJ or VS Code. Verify: <code>java -version</code>.</p><pre><code>public class Main {\n  public static void main(String[] args) {\n    System.out.println(\"Java is ready!\");\n  }\n}</code></pre>', 2),
(27, 3, 'Variables and Data Types', '<p>Java is <strong>statically typed</strong>. Primitives: <code>int</code>, <code>double</code>, <code>float</code>, <code>long</code>, <code>boolean</code>, <code>char</code>, <code>byte</code>, <code>short</code>.</p><pre><code>int age = 20;\ndouble price = 99.99;\nboolean active = true;\nString name = \"Juan\";</code></pre>', 3),
(28, 3, 'Operators', '<p>Java supports arithmetic, relational, logical, and assignment operators.</p><pre><code>int a = 10, b = 3;\nSystem.out.println(a + b);  // 13\nSystem.out.println(a % b);  // 1\nSystem.out.println(a > b);  // true</code></pre>', 4),
(29, 3, 'User Input with Scanner', '<p>Use the <code>Scanner</code> class from <code>java.util</code> to read user input.</p><pre><code>import java.util.Scanner;\nScanner sc = new Scanner(System.in);\nString name = sc.nextLine();\nSystem.out.println(\"Hello, \" + name);\nsc.close();</code></pre>', 5),
(30, 3, 'If-Else and Switch', '<p>Control flow using <code>if</code>, <code>else if</code>, <code>else</code>, and <code>switch</code>.</p><pre><code>int score = 85;\nif (score >= 90) {\n  System.out.println(\"Excellent\");\n} else if (score >= 75) {\n  System.out.println(\"Good\");\n} else {\n  System.out.println(\"Keep studying\");\n}</code></pre>', 6),
(31, 3, 'For and While Loops', '<p>Java has <code>for</code>, <code>while</code>, and <code>do-while</code> loops.</p><pre><code>for (int i = 1; i <= 5; i++) {\n  System.out.println(i);\n}\nint x = 0;\nwhile (x < 3) { System.out.println(x); x++; }\ndo { System.out.println(\"once\"); } while (false);</code></pre>', 7),
(32, 3, 'Arrays', '<p>Arrays store multiple values of the same type in a fixed-size structure.</p><pre><code>int[] scores = {90, 85, 78, 92, 88};\nSystem.out.println(scores[0]);     // 90\nSystem.out.println(scores.length); // 5\nfor (int s : scores) System.out.println(s);</code></pre>', 8),
(33, 3, 'Methods', '<p>Methods are reusable code blocks that accept parameters and return values.</p><pre><code>public static int add(int a, int b) { return a + b; }\npublic static void greet(String name) {\n  System.out.println(\"Hello, \" + name);\n}\nint result = add(5, 3);\ngreet(\"Juan\");</code></pre>', 9),
(34, 3, 'String Methods', '<p>The <code>String</code> class has many built-in methods for text manipulation.</p><pre><code>String s = \"Hello, StudyHub!\";\nSystem.out.println(s.length());       // 16\nSystem.out.println(s.toUpperCase());  // HELLO, STUDYHUB!\nSystem.out.println(s.contains(\"Study\")); // true\nSystem.out.println(s.substring(7, 15)); // StudyHub</code></pre>', 10),
(35, 3, 'Classes and Objects', '<p>A <strong>class</strong> is a blueprint; an <strong>object</strong> is an instance of it.</p><pre><code>public class Student {\n  String name;\n  int age;\n  public Student(String name, int age) {\n    this.name = name; this.age = age;\n  }\n  public void display() {\n    System.out.println(name + \" - \" + age);\n  }\n}\nStudent s = new Student(\"Juan\", 20);\ns.display();</code></pre>', 11),
(36, 3, 'Encapsulation', '<p>Hide data with <code>private</code> and expose it through <strong>getters and setters</strong>.</p><pre><code>public class Person {\n  private String name;\n  public String getName() { return name; }\n  public void setName(String n) { this.name = n; }\n}\nPerson p = new Person();\np.setName(\"Maria\");\nSystem.out.println(p.getName());</code></pre>', 12),
(37, 3, 'Inheritance', '<p>A child class inherits from a parent using <code>extends</code>. Use <code>@Override</code> to redefine methods.</p><pre><code>class Animal {\n  public void speak() { System.out.println(\"Some sound\"); }\n}\nclass Dog extends Animal {\n  @Override\n  public void speak() { System.out.println(\"Woof!\"); }\n}\nnew Dog().speak();</code></pre>', 13),
(38, 3, 'Polymorphism', '<p>Same method behaves differently based on the object. Achieved through overloading and overriding.</p><pre><code>public int add(int a, int b) { return a + b; }\npublic double add(double a, double b) { return a + b; }\n\nclass Cat extends Animal {\n  public void speak() { System.out.println(\"Meow!\"); }\n}</code></pre>', 14),
(39, 3, 'Interfaces and Abstraction', '<p>Interfaces define a contract. Abstract classes cannot be instantiated directly.</p><pre><code>interface Shape { double area(); }\nclass Circle implements Shape {\n  double r;\n  Circle(double r) { this.r = r; }\n  public double area() { return Math.PI * r * r; }\n}\nShape s = new Circle(5);\nSystem.out.println(s.area());</code></pre>', 15),
(40, 3, 'Exception Handling', '<p>Use <code>try</code>, <code>catch</code>, <code>finally</code> to handle runtime errors.</p><pre><code>try {\n  int result = 10 / 0;\n} catch (ArithmeticException e) {\n  System.out.println(\"Cannot divide by zero!\");\n} finally {\n  System.out.println(\"Always runs.\");\n}</code></pre>', 16),
(41, 3, 'ArrayList', '<p><code>ArrayList</code> is a resizable list from <code>java.util</code>.</p><pre><code>import java.util.ArrayList;\nimport java.util.Collections;\nArrayList list = new ArrayList();\nlist.add(\"Banana\"); list.add(\"Apple\");\nCollections.sort(list);\nSystem.out.println(list);\nSystem.out.println(list.size());</code></pre>', 17),
(42, 3, 'HashMap', '<p><code>HashMap</code> stores key-value pairs. Keys must be unique.</p><pre><code>import java.util.HashMap;\nHashMap grades = new HashMap();\ngrades.put(\"Juan\", 90);\ngrades.put(\"Maria\", 85);\nSystem.out.println(grades.get(\"Juan\"));\nfor (Object k : grades.keySet())\n  System.out.println(k + \": \" + grades.get(k));</code></pre>', 18),
(43, 3, 'File Handling', '<p>Read and write files using <code>FileWriter</code> and <code>BufferedReader</code>.</p><pre><code>import java.io.*;\nFileWriter fw = new FileWriter(\"notes.txt\");\nfw.write(\"StudyHub!\"); fw.close();\nBufferedReader br = new BufferedReader(new FileReader(\"notes.txt\"));\nString line;\nwhile ((line = br.readLine()) != null) System.out.println(line);\nbr.close();</code></pre>', 19),
(44, 3, 'Java Best Practices', '<p>Key rules for clean Java code:</p><ul><li>camelCase for variables/methods, PascalCase for classes</li><li>One responsibility per class/method</li><li>Always handle exceptions properly</li><li>Use meaningful names</li><li>Comment WHY, not WHAT</li></ul>', 20),
(45, 4, 'What is Python?', '<p>Python is a high-level, interpreted, dynamically typed language by Guido van Rossum (1991). Used in web dev, data science, AI, and automation.</p><pre><code>print(\"Hello, StudyHub!\")</code></pre>', 1),
(46, 4, 'Installing Python', '<p>Download from <strong>python.org</strong>. Use VS Code or PyCharm. Verify: <code>python --version</code>. Run files: <code>python filename.py</code></p>', 2),
(47, 4, 'Variables and Data Types', '<p>Python is <strong>dynamically typed</strong> — no type declarations needed. Use <code>type()</code> to check.</p><pre><code>name   = \"Maria\"       # str\nage    = 21            # int\nheight = 5.4           # float\nactive = True          # bool\nscores = [90, 85, 78]  # list\ninfo   = {\"city\": \"Manila\"} # dict</code></pre>', 3),
(48, 4, 'Operators', '<p>Python supports arithmetic, comparison, and logical operators.</p><pre><code>a, b = 10, 3\nprint(a + b)          # 13\nprint(a ** b)         # 1000\nprint(a // b)         # 3\nprint(a % b)          # 1\nprint(a > b and b > 0)  # True</code></pre>', 4),
(49, 4, 'User Input', '<p>Use <code>input()</code> to get user input. Always returns a string — convert as needed.</p><pre><code>name  = input(\"Enter name: \")\nage   = int(input(\"Enter age: \"))\nprice = float(input(\"Enter price: \"))\nprint(\"Hello,\", name)</code></pre>', 5),
(50, 4, 'If-Elif-Else', '<p>Python uses <strong>indentation</strong> to define blocks — no braces.</p><pre><code>score = 88\nif score >= 90:\n    print(\"Excellent!\")\nelif score >= 75:\n    print(\"Good job!\")\nelif score >= 50:\n    print(\"Passed\")\nelse:\n    print(\"Failed\")</code></pre>', 6),
(51, 4, 'For and While Loops', '<p>Use <code>for</code> to iterate over sequences, <code>while</code> for condition-based repetition.</p><pre><code>for i in range(1, 6):\n    print(i)\n\nfruits = [\"apple\", \"mango\"]\nfor fruit in fruits:\n    print(fruit)\n\ncount = 0\nwhile count < 3:\n    print(count)\n    count += 1</code></pre>', 7),
(52, 4, 'Functions', '<p>Define with <code>def</code>. Support default params and multiple return values.</p><pre><code>def greet(name, greeting=\"Hello\"):\n    return f\"{greeting}, {name}!\"\n\nprint(greet(\"Juan\"))\nprint(greet(\"Maria\", \"Hi\"))\n\ndef min_max(nums):\n    return min(nums), max(nums)\nlo, hi = min_max([3,1,9,5])</code></pre>', 8),
(53, 4, 'Lists', '<p>An ordered, mutable collection that can hold mixed types.</p><pre><code>fruits = [\"apple\", \"banana\", \"mango\"]\nfruits.append(\"grape\")\nfruits.insert(1, \"cherry\")\nfruits.remove(\"banana\")\nprint(fruits[0])   # apple\nprint(len(fruits)) # 4\nfruits.sort()</code></pre>', 9),
(54, 4, 'Tuples and Sets', '<p><strong>Tuple</strong> — ordered, immutable. <strong>Set</strong> — unordered, unique elements only.</p><pre><code>coords = (10.5, 123.9)\nprint(coords[0])   # 10.5\n\nnums = {1, 2, 3, 3, 2}\nprint(nums)        # {1, 2, 3}\nnums.add(4)\nprint(1 in nums)</code></pre>', 10),
(55, 4, 'Dictionaries', '<p>Stores key-value pairs. Keys must be unique and immutable.</p><pre><code>student = {\"name\": \"Juan\", \"age\": 20}\nprint(student[\"name\"])        # Juan\nstudent[\"age\"] = 21           # update\nstudent[\"school\"] = \"StudyHub\"\ndel student[\"age\"]\nfor k, v in student.items():\n    print(k, \":\", v)</code></pre>', 11),
(56, 4, 'String Methods', '<p>Python strings have many powerful built-in methods.</p><pre><code>s = \"  Hello, StudyHub!  \"\nprint(s.strip())\nprint(s.upper())\nprint(s.replace(\"Hello\", \"Hi\"))\nprint(s.split(\",\"))\nprint(\"Study\" in s)   # True</code></pre>', 12),
(57, 4, 'Classes and Objects', '<p><code>class</code> defines a blueprint. <code>__init__</code> is the constructor. <code>self</code> refers to the current instance.</p><pre><code>class Student:\n    def __init__(self, name, age):\n        self.name = name\n        self.age  = age\n    def display(self):\n        print(f\"{self.name} is {self.age} years old\")\n\ns = Student(\"Juan\", 20)\ns.display()</code></pre>', 13),
(58, 4, 'Inheritance', '<p>Child class inherits from parent. Use <code>super()</code> to call the parent constructor.</p><pre><code>class Animal:\n    def __init__(self, name): self.name = name\n    def speak(self): print(self.name, \"makes a sound\")\n\nclass Dog(Animal):\n    def speak(self): print(self.name, \"barks!\")\n\nDog(\"Rex\").speak()</code></pre>', 14),
(59, 4, 'File Handling', '<p>Use <code>open()</code> with <code>with</code> to safely read and write files.</p><pre><code>with open(\"notes.txt\", \"w\") as f:\n    f.write(\"StudyHub!\n\")\n\nwith open(\"notes.txt\", \"r\") as f:\n    print(f.read())\n\nwith open(\"notes.txt\", \"a\") as f:\n    f.write(\"Keep learning!\")</code></pre>', 15),
(60, 4, 'Exception Handling', '<p>Use <code>try</code>, <code>except</code>, <code>else</code>, <code>finally</code> to handle errors.</p><pre><code>try:\n    num = int(input(\"Enter a number: \"))\n    print(10 / num)\nexcept ValueError:\n    print(\"Not a number!\")\nexcept ZeroDivisionError:\n    print(\"Cannot divide by zero!\")\nfinally:\n    print(\"Done.\")</code></pre>', 16),
(61, 4, 'List Comprehensions', '<p>A concise way to create lists from iterables.</p><pre><code>squares = [i ** 2 for i in range(1, 6)]\nprint(squares)  # [1, 4, 9, 16, 25]\n\nevens = [i for i in range(20) if i % 2 == 0]\nprint(evens)\n\nwords = [\"hello\", \"world\"]\nupper = [w.upper() for w in words]</code></pre>', 17),
(62, 4, 'Lambda and Map/Filter', '<p>Lambda is an anonymous function. <code>map()</code> and <code>filter()</code> apply functions to iterables.</p><pre><code>double = lambda x: x * 2\nprint(double(5))  # 10\n\nnums = [1, 2, 3, 4, 5]\ndoubled = list(map(lambda x: x * 2, nums))\nodds    = list(filter(lambda x: x % 2 != 0, nums))\nprint(doubled)\nprint(odds)</code></pre>', 18),
(63, 4, 'Modules and Packages', '<p>Import built-in or third-party modules using <code>import</code>.</p><pre><code>import math, random, datetime\nprint(math.sqrt(25))          # 5.0\nprint(math.pi)                # 3.14159\nprint(random.randint(1, 10))  # random\nprint(datetime.date.today())\nfrom math import factorial\nprint(factorial(5))           # 120</code></pre>', 19),
(64, 4, 'Python Best Practices', '<p>Follow PEP 8 style guidelines:</p><ul><li>snake_case for variables/functions, PascalCase for classes</li><li>Max 79 characters per line</li><li>Use docstrings to document functions</li><li>Prefer list comprehensions when readable</li><li>Use <code>with</code> for file/resource handling</li></ul><pre><code>def calculate_area(radius):\n    \"\"\"Return the area of a circle.\"\"\"\n    return 3.14159 * radius ** 2</code></pre>', 20),
(65, 5, 'What is C++?', '<p>C++ (1985, Bjarne Stroustrup) is a general-purpose language extending C. Supports <strong>procedural</strong> and <strong>object-oriented</strong> programming. Used in games, OS, and embedded systems.</p>', 1),
(66, 5, 'Setting Up C++', '<p>Install <strong>GCC</strong> via MinGW on Windows, or use Code::Blocks/Visual Studio. Compile: <code>g++ main.cpp -o main</code>. Run: <code>./main</code></p>', 2),
(67, 5, 'Variables and Data Types', '<p>C++ is statically typed. Declare types before use.</p><pre><code>int age = 20;\nfloat gpa = 3.75f;\ndouble pi = 3.14159265;\nchar grade = 65;\nbool passed = true;\nstring name = \"Juan\";</code></pre>', 3),
(68, 5, 'Operators', '<p>C++ supports arithmetic, relational, logical, bitwise, and compound assignment operators.</p><pre><code>int a = 10, b = 3;\ncout << a + b;    // 13\ncout << a % b;    // 1\ncout << (a > b);  // 1\na += 5;           // a = 15\na++;              // a = 16</code></pre>', 4),
(69, 5, 'User Input with cin', '<p>Use <code>cin</code> for reading input and <code>cout</code> for output, both from <code>iostream</code>.</p><pre><code>#include &lt;iostream&gt;\n#include &lt;string&gt;\nusing namespace std;\nint main() {\n    string name; int age;\n    cout &lt;&lt; \"Enter name: \"; cin >> name;\n    cout &lt;&lt; \"Enter age: \";  cin >> age;\n    cout &lt;&lt; \"Hello \" &lt;&lt; name &lt;&lt; endl;\n    return 0;\n}</code></pre>', 5),
(70, 5, 'If-Else and Switch', '<p>C++ uses braces for code blocks. Supports <code>if</code>, <code>else if</code>, <code>else</code>, and <code>switch</code>.</p><pre><code>int score = 85;\nif (score >= 90)       cout &lt;&lt; \"Excellent\";\nelse if (score >= 75)  cout &lt;&lt; \"Good\";\nelse if (score >= 50)  cout &lt;&lt; \"Passed\";\nelse                   cout &lt;&lt; \"Failed\";</code></pre>', 6),
(71, 5, 'For and While Loops', '<p>C++ has <code>for</code>, <code>while</code>, and <code>do-while</code> loops.</p><pre><code>for (int i = 1; i &lt;= 5; i++)\n    cout &lt;&lt; i &lt;&lt; \" \";\n\nint x = 0;\nwhile (x &lt; 3) { cout &lt;&lt; x++ &lt;&lt; \" \"; }\n\ndo { cout &lt;&lt; \"once\"; } while (false);</code></pre>', 7),
(72, 5, 'Arrays', '<p>Arrays store multiple values of the same type. Size is fixed at compile time.</p><pre><code>int scores[5] = {90, 85, 78, 92, 88};\ncout &lt;&lt; scores[0];   // 90\nfor (int i = 0; i &lt; 5; i++)\n    cout &lt;&lt; scores[i] &lt;&lt; \" \";\nfor (int s : scores) cout &lt;&lt; s &lt;&lt; \" \";</code></pre>', 8),
(73, 5, 'Functions', '<p>Functions must be declared before use or use a prototype. Can return values or be <code>void</code>.</p><pre><code>int add(int a, int b) { return a + b; }\nvoid greet(string name = \"Student\") {\n    cout &lt;&lt; \"Hello, \" &lt;&lt; name &lt;&lt; \"!\";\n}\nint main() {\n    cout &lt;&lt; add(5, 3);\n    greet(\"Juan\");\n    return 0;\n}</code></pre>', 9),
(74, 5, 'Strings in C++', '<p>Use the <code>string</code> class (preferred over C-style char arrays).</p><pre><code>#include &lt;string&gt;\nstring s = \"Hello, StudyHub!\";\ncout &lt;&lt; s.length();          // 16\ncout &lt;&lt; s.substr(7, 8);     // StudyHub\ncout &lt;&lt; s.find(\"Hub\");      // 12\ns.replace(0, 5, \"Hi\");\ncout &lt;&lt; s;</code></pre>', 10),
(75, 5, 'Pointers', '<p>Pointers store memory addresses. Use <code>*</code> to declare/dereference, <code>&amp;</code> to get the address.</p><pre><code>int num = 42;\nint* ptr = &num;\ncout &lt;&lt; *ptr;    // 42\n*ptr = 100;\ncout &lt;&lt; num;     // 100\nint* p = nullptr;</code></pre>', 11),
(76, 5, 'References', '<p>A reference is an alias for another variable. Cannot be null or reassigned.</p><pre><code>int x = 10;\nint& ref = x;\nref = 50;\ncout &lt;&lt; x;  // 50\n\nvoid doubleIt(int& n) { n *= 2; }\nint val = 5;\ndoubleIt(val);\ncout &lt;&lt; val; // 10</code></pre>', 12),
(77, 5, 'Classes and Objects', '<p>A class groups data and functions. An object is an instance of a class.</p><pre><code>class Student {\npublic:\n    string name; int age;\n    Student(string n, int a) { name=n; age=a; }\n    void display() {\n        cout &lt;&lt; name &lt;&lt; \" | \" &lt;&lt; age &lt;&lt; endl;\n    }\n};\nStudent s(\"Juan\", 20);\ns.display();</code></pre>', 13),
(78, 5, 'Encapsulation', '<p>Use <code>private</code> to hide data and <code>public</code> getter/setter methods to safely expose it.</p><pre><code>class BankAccount {\nprivate:\n    double balance;\npublic:\n    BankAccount(double b) { balance = b; }\n    double getBalance() { return balance; }\n    void deposit(double amt) {\n        if (amt > 0) balance += amt;\n    }\n};\nBankAccount acc(1000);\nacc.deposit(500);\ncout &lt;&lt; acc.getBalance(); // 1500</code></pre>', 14),
(79, 5, 'Inheritance', '<p>Derived class inherits from base using <code>:</code>. Use <code>virtual</code> for overridable methods.</p><pre><code>class Animal {\npublic:\n    string name;\n    Animal(string n) : name(n) {}\n    virtual void speak() { cout &lt;&lt; name &lt;&lt; \" makes a sound\"; }\n};\nclass Dog : public Animal {\npublic:\n    Dog(string n) : Animal(n) {}\n    void speak() override { cout &lt;&lt; name &lt;&lt; \" barks!\"; }\n};\nDog d(\"Rex\"); d.speak();</code></pre>', 15),
(80, 5, 'Polymorphism and Virtual Functions', '<p>Virtual functions enable runtime polymorphism. Pure virtual (<code>= 0</code>) makes a class abstract.</p><pre><code>class Shape {\npublic:\n    virtual double area() = 0;\n};\nclass Circle : public Shape {\n    double r;\npublic:\n    Circle(double r) : r(r) {}\n    double area() override { return 3.14 * r * r; }\n};\nShape* s = new Circle(5);\ncout &lt;&lt; s->area();\ndelete s;</code></pre>', 16),
(81, 5, 'Vectors', '<p><code>vector</code> from STL is a resizable dynamic array — preferred over raw arrays.</p><pre><code>#include &lt;vector&gt;\n#include &lt;algorithm&gt;\nvector&lt;int&gt; nums = {5, 2, 8, 1, 9};\nnums.push_back(4);\nnums.pop_back();\nsort(nums.begin(), nums.end());\ncout &lt;&lt; nums.size();\nfor (int n : nums) cout &lt;&lt; n &lt;&lt; \" \";</code></pre>', 17),
(82, 5, 'File Handling', '<p>Use <code>fstream</code> for file I/O. <code>ofstream</code> writes, <code>ifstream</code> reads.</p><pre><code>#include &lt;fstream&gt;\nofstream out(\"notes.txt\");\nout &lt;&lt; \"StudyHub!\" &lt;&lt; endl;\nout.close();\nifstream in(\"notes.txt\");\nstring line;\nwhile (getline(in, line)) cout &lt;&lt; line &lt;&lt; endl;\nin.close();</code></pre>', 18),
(83, 5, 'Templates', '<p>Templates allow generic functions and classes that work with any data type.</p><pre><code>template &lt;typename T&gt;\nT add(T a, T b) { return a + b; }\ncout &lt;&lt; add(3, 5);       // 8\ncout &lt;&lt; add(2.5, 1.5);  // 4.0\n\ntemplate &lt;class T&gt;\nclass Box {\npublic:\n    T value;\n    Box(T v) : value(v) {}\n};\nBox&lt;int&gt; b(42);\ncout &lt;&lt; b.value;</code></pre>', 19),
(84, 5, 'C++ Best Practices', '<p>Key principles for clean C++ code:</p><ul><li>Prefer smart pointers over raw pointers</li><li>Use <code>const</code> wherever values should not change</li><li>Prefer STL containers over raw arrays</li><li>Use references instead of pointers when possible</li><li>Always initialize variables before use</li></ul>', 20),
(85, 1, 'PHP Arrays', '<p>Arrays in PHP store multiple values in a single variable. PHP supports indexed, associative, and multidimensional arrays.</p><pre><code>// Indexed array\n$fruits = [\"apple\", \"banana\", \"mango\"];\necho $fruits[0]; // apple\n\n// Associative array\n$student = [\"name\" => \"Juan\", \"age\" => 20];\necho $student[\"name\"]; // Juan\n\n// Count elements\necho count($fruits); // 3</code></pre>', 6),
(86, 1, 'PHP Loops', '<p>PHP supports <code>for</code>, <code>while</code>, <code>do-while</code>, and <code>foreach</code> loops. Use <code>foreach</code> to iterate over arrays easily.</p><pre><code>$scores = [90, 85, 78, 92];\n\n// foreach loop\nforeach ($scores as $score) {\n    echo $score . \" \";\n}\n\n// for loop\nfor ($i = 0; $i < count($scores); $i++) {\n    echo $scores[$i] . \" \";\n}\n\n// while loop\n$i = 0;\nwhile ($i < 3) {\n    echo $i;\n    $i++;\n}</code></pre>', 7),
(87, 1, 'PHP String Functions', '<p>PHP has many built-in string functions to manipulate text easily.</p><pre><code>$str = \"Hello, StudyHub!\";\n\necho strlen($str);           // 16\necho strtoupper($str);       // HELLO, STUDYHUB!\necho strtolower($str);       // hello, studyhub!\necho str_replace(\"Hello\", \"Hi\", $str); // Hi, StudyHub!\necho substr($str, 7, 8);     // StudyHub\necho strpos($str, \"Study\"); // 7\necho trim(\"  hello  \");     // hello</code></pre>', 8),
(88, 1, 'PHP Superglobals', '<p>Superglobals are built-in variables always accessible in all scopes. Common ones: <code>$_GET</code>, <code>$_POST</code>, <code>$_SESSION</code>, <code>$_COOKIE</code>, <code>$_SERVER</code>, <code>$_FILES</code>.</p><pre><code>// $_GET — data from URL query string\n$name = $_GET[\"name\"]; // URL: page.php?name=Juan\n\n// $_SERVER — server info\necho $_SERVER[\"SERVER_NAME\"]; // localhost\necho $_SERVER[\"PHP_SELF\"];    // current file path\necho $_SERVER[\"REQUEST_METHOD\"]; // GET or POST</code></pre>', 9),
(89, 1, 'Sessions in PHP', '<p>Sessions store user data across multiple pages. Use <code>session_start()</code> at the top of every page that uses sessions.</p><pre><code>// Start session\nsession_start();\n\n// Set session variable\n$_SESSION[\"user\"] = \"Juan\";\n$_SESSION[\"role\"] = \"student\";\n\n// Read session\necho \"Welcome, \" . $_SESSION[\"user\"];\n\n// Destroy session (logout)\nsession_destroy();\nheader(\"Location: login.php\");\nexit;</code></pre>', 10),
(90, 1, 'Cookies in PHP', '<p>Cookies store small pieces of data in the browser. They persist across requests and can have an expiry time.</p><pre><code>// Set a cookie (expires in 1 hour)\nsetcookie(\"username\", \"Juan\", time() + 3600, \"/\");\n\n// Read a cookie\nif (isset($_COOKIE[\"username\"])) {\n    echo \"Hello, \" . $_COOKIE[\"username\"];\n}\n\n// Delete a cookie (set expiry in the past)\nsetcookie(\"username\", \"\", time() - 3600, \"/\");</code></pre>', 11),
(91, 1, 'PHP and MySQL — Connecting', '<p>Use <code>mysqli_connect()</code> to connect PHP to a MySQL database. Always check for connection errors.</p><pre><code>$conn = mysqli_connect(\"localhost\", \"root\", \"\", \"studyhub\");\n\nif (!$conn) {\n    die(\"Connection failed: \" . mysqli_connect_error());\n}\n\necho \"Connected successfully!\";\n\n// Close connection\nmysqli_close($conn);</code></pre>', 12),
(92, 1, 'PHP — Fetching Data from MySQL', '<p>Use <code>mysqli_query()</code> to run SQL and <code>mysqli_fetch_assoc()</code> to read results row by row.</p><pre><code>$result = mysqli_query($conn, \"SELECT * FROM users\");\n\nwhile ($row = mysqli_fetch_assoc($result)) {\n    echo $row[\"name\"] . \" - \" . $row[\"email\"];\n    echo \"&lt;br&gt;\";\n}\n\n// Count rows\n$total = mysqli_num_rows($result);\necho \"Total: \" . $total;</code></pre>', 13),
(93, 1, 'PHP — Inserting Data into MySQL', '<p>Use prepared statements to safely insert data and prevent SQL injection.</p><pre><code>$name  = $_POST[\"name\"];\n$email = $_POST[\"email\"];\n\n$stmt = mysqli_prepare($conn,\n    \"INSERT INTO users (name, email) VALUES (?, ?)\");\n\nmysqli_stmt_bind_param($stmt, \"ss\", $name, $email);\n\nif (mysqli_stmt_execute($stmt)) {\n    echo \"User added!\";\n} else {\n    echo \"Error: \" . mysqli_error($conn);\n}</code></pre>', 14),
(94, 1, 'PHP — Updating and Deleting Data', '<p>Use <code>UPDATE</code> and <code>DELETE</code> SQL with prepared statements to safely modify your database.</p><pre><code>// UPDATE\n$stmt = mysqli_prepare($conn,\n    \"UPDATE users SET name = ? WHERE id = ?\");\nmysqli_stmt_bind_param($stmt, \"si\", $name, $id);\nmysqli_stmt_execute($stmt);\n\n// DELETE\n$stmt = mysqli_prepare($conn,\n    \"DELETE FROM users WHERE id = ?\");\nmysqli_stmt_bind_param($stmt, \"i\", $id);\nmysqli_stmt_execute($stmt);\necho \"Deleted!\";</code></pre>', 15),
(95, 1, 'PHP File Handling', '<p>PHP can create, read, write, and delete files on the server using built-in functions.</p><pre><code>// Write to file\nfile_put_contents(\"notes.txt\", \"StudyHub is great!\");\n\n// Read from file\n$content = file_get_contents(\"notes.txt\");\necho $content;\n\n// Append to file\nfile_put_contents(\"notes.txt\", \"\nKeep learning!\", FILE_APPEND);\n\n// Check if file exists\nif (file_exists(\"notes.txt\")) {\n    echo \"File found!\";\n    unlink(\"notes.txt\"); // delete\n}</code></pre>', 16),
(96, 1, 'PHP Error Handling', '<p>Use <code>try</code>, <code>catch</code>, and <code>throw</code> to handle exceptions and prevent crashes.</p><pre><code>function divide($a, $b) {\n    if ($b == 0) {\n        throw new Exception(\"Cannot divide by zero!\");\n    }\n    return $a / $b;\n}\n\ntry {\n    echo divide(10, 2); // 5\n    echo divide(10, 0); // throws\n} catch (Exception $e) {\n    echo \"Error: \" . $e->getMessage();\n} finally {\n    echo \"Done.\";\n}</code></pre>', 17),
(97, 1, 'PHP Object-Oriented Programming', '<p>PHP supports OOP with classes, objects, constructors, inheritance, and access modifiers.</p><pre><code>class Student {\n    private $name;\n    private $grade;\n\n    public function __construct($name, $grade) {\n        $this->name  = $name;\n        $this->grade = $grade;\n    }\n\n    public function display() {\n        echo $this->name . \" — Grade: \" . $this->grade;\n    }\n}\n\n$s = new Student(\"Juan\", \"A\");\n$s->display();</code></pre>', 18),
(98, 1, 'PHP Security Best Practices', '<p>Always sanitize user input, use prepared statements, and never trust data from forms or URLs.</p><pre><code>// Sanitize input\n$name = htmlspecialchars(trim($_POST[\"name\"]));\n$email = filter_var($_POST[\"email\"], FILTER_VALIDATE_EMAIL);\n\nif (!$email) {\n    die(\"Invalid email address.\");\n}\n\n// Never use this (SQL injection risk):\n// \"SELECT * FROM users WHERE id = \" . $_GET[\"id\"]\n\n// Always use prepared statements instead\n$stmt = mysqli_prepare($conn, \"SELECT * FROM users WHERE id = ?\");\nmysqli_stmt_bind_param($stmt, \"i\", $id);</code></pre>', 19),
(99, 1, 'Building a Simple Login System', '<p>A basic PHP login flow checks credentials against the database and stores session data on success.</p><pre><code>session_start();\n\nif ($_SERVER[\"REQUEST_METHOD\"] == \"POST\") {\n    $email = trim($_POST[\"email\"]);\n    $pass  = md5(trim($_POST[\"password\"]));\n\n    $stmt = mysqli_prepare($conn,\n        \"SELECT id, name FROM users WHERE email=? AND password=?\");\n    mysqli_stmt_bind_param($stmt, \"ss\", $email, $pass);\n    mysqli_stmt_execute($stmt);\n    $result = mysqli_stmt_get_result($stmt);\n    $user   = mysqli_fetch_assoc($result);\n\n    if ($user) {\n        $_SESSION[\"user_id\"]   = $user[\"id\"];\n        $_SESSION[\"user_name\"] = $user[\"name\"];\n        header(\"Location: dashboard.php\");\n    } else {\n        echo \"Invalid credentials.\";\n    }\n}</code></pre>', 20),
(100, 2, 'GROUP BY and Aggregate Functions', '<p>Use <code>GROUP BY</code> to group rows and aggregate functions like <code>COUNT</code>, <code>SUM</code>, <code>AVG</code>, <code>MIN</code>, <code>MAX</code> to summarize data.</p><pre><code>-- Count students per course\nSELECT course_id, COUNT(*) AS total\nFROM enrollments\nGROUP BY course_id;\n\n-- Average score per course\nSELECT course_id, AVG(score) AS avg_score\nFROM quiz_results\nGROUP BY course_id;\n\n-- Total and max score\nSELECT SUM(score), MAX(score), MIN(score)\nFROM quiz_results;</code></pre>', 5),
(101, 2, 'HAVING Clause', '<p><code>HAVING</code> filters grouped results — like <code>WHERE</code> but used after <code>GROUP BY</code>.</p><pre><code>-- Courses with more than 5 students\nSELECT course_id, COUNT(*) AS total\nFROM enrollments\nGROUP BY course_id\nHAVING total > 5;\n\n-- Users with average score above 75\nSELECT user_id, AVG(score) AS avg\nFROM quiz_results\nGROUP BY user_id\nHAVING avg > 75;</code></pre>', 6),
(102, 2, 'Subqueries', '<p>A subquery is a query nested inside another query. It can appear in <code>SELECT</code>, <code>FROM</code>, or <code>WHERE</code> clauses.</p><pre><code>-- Users who scored above average\nSELECT name FROM users\nWHERE id IN (\n    SELECT user_id FROM quiz_results\n    WHERE score > (\n        SELECT AVG(score) FROM quiz_results\n    )\n);\n\n-- Count using subquery\nSELECT COUNT(*) FROM (\n    SELECT user_id FROM quiz_results\n    GROUP BY user_id\n) AS unique_users;</code></pre>', 7),
(103, 2, 'Data Types in MySQL', '<p>Choosing the right data type improves performance and storage efficiency.</p><pre><code>-- Integer types\nTINYINT    -- -128 to 127\nSMALLINT   -- -32768 to 32767\nINT        -- ~2.1 billion\nBIGINT     -- very large numbers\n\n-- String types\nVARCHAR(100)  -- variable length up to 100\nTEXT          -- long text\nCHAR(10)      -- fixed length\n\n-- Date/time\nDATE          -- YYYY-MM-DD\nDATETIME      -- YYYY-MM-DD HH:MM:SS\nTIMESTAMP     -- auto-updates on change</code></pre>', 8),
(104, 2, 'Creating and Altering Tables', '<p>Use <code>CREATE TABLE</code> to define tables and <code>ALTER TABLE</code> to modify them after creation.</p><pre><code>-- Create table\nCREATE TABLE products (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    name VARCHAR(100) NOT NULL,\n    price DECIMAL(10,2),\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n);\n\n-- Add a column\nALTER TABLE products ADD COLUMN stock INT DEFAULT 0;\n\n-- Modify a column\nALTER TABLE products MODIFY COLUMN name VARCHAR(200);\n\n-- Drop a column\nALTER TABLE products DROP COLUMN stock;</code></pre>', 9),
(105, 2, 'Constraints', '<p>Constraints enforce rules on data. Common constraints: <code>PRIMARY KEY</code>, <code>FOREIGN KEY</code>, <code>UNIQUE</code>, <code>NOT NULL</code>, <code>DEFAULT</code>, <code>CHECK</code>.</p><pre><code>CREATE TABLE orders (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    user_id INT NOT NULL,\n    total DECIMAL(10,2) DEFAULT 0.00,\n    status VARCHAR(20) DEFAULT \"pending\",\n    email VARCHAR(150) UNIQUE,\n    CONSTRAINT chk_total CHECK (total >= 0),\n    FOREIGN KEY (user_id) REFERENCES users(id)\n);</code></pre>', 10),
(106, 2, 'Normalization', '<p>Normalization organizes data to reduce redundancy. Key normal forms:</p><p><strong>1NF</strong> — No repeating groups, each column has atomic values.<br><strong>2NF</strong> — 1NF + no partial dependencies on primary key.<br><strong>3NF</strong> — 2NF + no transitive dependencies.</p><pre><code>-- Bad (not normalized):\nstudent_name | course1 | course2 | course3\n\n-- Good (normalized):\nstudents: id, name\ncourses:  id, title\nenrollments: student_id, course_id</code></pre>', 11),
(107, 2, 'Transactions', '<p>Transactions group SQL statements so they all succeed or all fail together. Use <code>COMMIT</code> to save and <code>ROLLBACK</code> to undo.</p><pre><code>START TRANSACTION;\n\nUPDATE accounts SET balance = balance - 500 WHERE id = 1;\nUPDATE accounts SET balance = balance + 500 WHERE id = 2;\n\n-- If both succeed:\nCOMMIT;\n\n-- If something goes wrong:\nROLLBACK;\n\n-- Check auto-commit setting\nSHOW VARIABLES LIKE \"autocommit\";</code></pre>', 12),
(108, 2, 'Views', '<p>A <strong>view</strong> is a saved SQL query that acts like a virtual table. It simplifies complex queries and controls data access.</p><pre><code>-- Create a view\nCREATE VIEW student_scores AS\nSELECT u.name, c.title, qr.score\nFROM quiz_results qr\nJOIN users u ON qr.user_id = u.id\nJOIN courses c ON qr.course_id = c.id;\n\n-- Use the view like a table\nSELECT * FROM student_scores WHERE score > 80;\n\n-- Drop the view\nDROP VIEW student_scores;</code></pre>', 13),
(109, 2, 'Stored Procedures', '<p>A stored procedure is a saved block of SQL you can call by name. Good for reusable logic.</p><pre><code>-- Create procedure\nDELIMITER //\nCREATE PROCEDURE GetUserScores(IN uid INT)\nBEGIN\n    SELECT c.title, qr.score, qr.taken_at\n    FROM quiz_results qr\n    JOIN courses c ON qr.course_id = c.id\n    WHERE qr.user_id = uid\n    ORDER BY qr.taken_at DESC;\nEND //\nDELIMITER ;\n\n-- Call the procedure\nCALL GetUserScores(1);</code></pre>', 14),
(110, 2, 'Triggers', '<p>A <strong>trigger</strong> automatically executes SQL when a specific event (INSERT, UPDATE, DELETE) happens on a table.</p><pre><code>-- Log every new quiz result automatically\nDELIMITER //\nCREATE TRIGGER after_quiz_insert\nAFTER INSERT ON quiz_results\nFOR EACH ROW\nBEGIN\n    INSERT INTO activity_log (user_id, action, created_at)\n    VALUES (NEW.user_id, \"Took quiz\", NOW());\nEND //\nDELIMITER ;\n\n-- View triggers\nSHOW TRIGGERS;</code></pre>', 15),
(111, 2, 'User Management and Privileges', '<p>MySQL allows creating users and granting specific permissions to control database access.</p><pre><code>-- Create a new user\nCREATE USER \"studyhub_user\"@\"localhost\" IDENTIFIED BY \"password123\";\n\n-- Grant privileges\nGRANT SELECT, INSERT, UPDATE ON studyhub.* TO \"studyhub_user\"@\"localhost\";\n\n-- Show privileges\nSHOW GRANTS FOR \"studyhub_user\"@\"localhost\";\n\n-- Revoke a privilege\nREVOKE DELETE ON studyhub.* FROM \"studyhub_user\"@\"localhost\";\n\n-- Drop the user\nDROP USER \"studyhub_user\"@\"localhost\";</code></pre>', 16),
(112, 2, 'Backup and Restore', '<p>Regular backups protect your data. Use <code>mysqldump</code> to export and <code>mysql</code> to restore.</p><pre><code>-- Export (backup) entire database\nmysqldump -u root -p studyhub > backup.sql\n\n-- Export a single table\nmysqldump -u root -p studyhub users > users_backup.sql\n\n-- Restore from backup\nmysql -u root -p studyhub < backup.sql\n\n-- Export with no data (structure only)\nmysqldump -u root -p --no-data studyhub > structure.sql</code></pre>', 17),
(113, 2, 'Full-Text Search', '<p>MySQL supports full-text search for fast text searching across large text columns.</p><pre><code>-- Add FULLTEXT index\nALTER TABLE lessons ADD FULLTEXT(title, content);\n\n-- Natural language search\nSELECT id, title\nFROM lessons\nWHERE MATCH(title, content) AGAINST(\"PHP variables\");\n\n-- Boolean mode search\nSELECT id, title\nFROM lessons\nWHERE MATCH(title, content)\nAGAINST(\"+PHP -HTML\" IN BOOLEAN MODE);</code></pre>', 18),
(114, 2, 'Database Design Best Practices', '<p>Good database design makes applications faster, more reliable, and easier to maintain.</p><ul><li><strong>Use surrogate keys</strong> — Let AUTO_INCREMENT handle primary keys</li><li><strong>Normalize to 3NF</strong> — Eliminate data redundancy</li><li><strong>Index foreign keys</strong> — Always index columns used in JOINs</li><li><strong>Use appropriate data types</strong> — Do not store numbers as VARCHAR</li><li><strong>Name consistently</strong> — Use snake_case for table and column names</li><li><strong>Use NOT NULL</strong> — Enforce required fields at the DB level</li></ul>', 19),
(115, 2, 'MySQL Performance Tips', '<p>Optimize your database for speed and scalability.</p><pre><code>-- Use EXPLAIN to analyze a query\nEXPLAIN SELECT * FROM quiz_results WHERE user_id = 1;\n\n-- Add index on frequently searched column\nCREATE INDEX idx_user_id ON quiz_results(user_id);\n\n-- Avoid SELECT * — specify columns\nSELECT id, score, taken_at FROM quiz_results;\n\n-- Use LIMIT to paginate large results\nSELECT * FROM quiz_results ORDER BY taken_at DESC LIMIT 10 OFFSET 0;\n\n-- Cache repeated queries using views or stored procedures</code></pre>', 20),
(116, 1, 'programming', 'Programming is the process of giving instructions to a computer so it can perform specific tasks. These instructions are written in a programming language that both humans and computers can understand (with the help of a compiler or interpreter).', 21);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_answer` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `course_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`) VALUES
(203, 1, 'What does PHP stand for?', 'Personal Home Page', 'Hypertext Preprocessor', 'Programming Hypertext Protocol', 'Public Home Processor', 'b'),
(204, 1, 'Which symbol declares a PHP variable?', '#', '&', '$', '@', 'c'),
(205, 1, 'How do you end a PHP statement?', 'Period (.)', 'Colon (:)', 'Semicolon (;)', 'Comma (,)', 'c'),
(206, 1, 'Which function outputs text in PHP?', 'print_text()', 'console.log()', 'echo', 'write()', 'c'),
(207, 1, 'Which superglobal holds POST form data?', '$_GET', '$_POST', '$_SESSION', '$_FORM', 'b'),
(208, 1, 'What keyword defines a function in PHP?', 'def', 'func', 'function', 'method', 'c'),
(209, 1, 'Which loop iterates over an array in PHP?', 'for', 'foreach', 'while', 'do-while', 'b'),
(210, 1, 'Which operator concatenates strings in PHP?', '+', '&', '.', '~', 'c'),
(211, 1, 'How do you start a single-line comment in PHP?', '##', '<!--', '//', '**', 'c'),
(212, 1, 'Which function includes another PHP file and stops on error?', 'import()', 'require()', 'use()', 'load()', 'b'),
(213, 1, 'What does $_SERVER[\"REQUEST_METHOD\"] return?', 'Server IP', 'HTTP method (GET/POST)', 'User agent', 'Page URL', 'b'),
(214, 1, 'Which PHP function returns the length of a string?', 'length()', 'count()', 'strlen()', 'size()', 'c'),
(215, 1, 'Which superglobal stores session data in PHP?', '$_COOKIE', '$_POST', '$_SESSION', '$_DATA', 'c'),
(216, 1, 'What function sanitizes output to prevent XSS in PHP?', 'sanitize()', 'clean()', 'htmlspecialchars()', 'strip_tags()', 'c'),
(217, 1, 'How do you start a session in PHP?', 'start_session()', 'session_start()', 'begin_session()', 'open_session()', 'b'),
(218, 1, 'Which function converts a string to uppercase in PHP?', 'toUpper()', 'str_upper()', 'strtoupper()', 'uppercase()', 'c'),
(219, 1, 'What does the \"==\" operator do in PHP?', 'Assigns a value', 'Checks value equality (loose)', 'Checks value and type equality', 'Compares strings only', 'b'),
(220, 1, 'Which PHP function counts array elements?', 'length()', 'size()', 'count()', 'total()', 'c'),
(221, 1, 'What is the correct way to connect to MySQL in PHP?', 'mysql_open()', 'mysqli_connect()', 'db_connect()', 'sql_connect()', 'b'),
(222, 1, 'Which PHP construct is used for object-oriented programming?', 'module', 'struct', 'class', 'package', 'c'),
(223, 2, 'Which SQL command retrieves data from a table?', 'GET', 'FETCH', 'SELECT', 'SHOW', 'c'),
(224, 2, 'Which clause filters rows in a SELECT query?', 'HAVING', 'WHERE', 'FILTER', 'LIMIT', 'b'),
(225, 2, 'What does AUTO_INCREMENT do in MySQL?', 'Duplicates a row', 'Automatically numbers new rows', 'Sorts the table', 'Encrypts IDs', 'b'),
(226, 2, 'Which JOIN returns all rows from the left table?', 'INNER JOIN', 'RIGHT JOIN', 'LEFT JOIN', 'FULL JOIN', 'c'),
(227, 2, 'Which command inserts a new row into a table?', 'ADD', 'PUT', 'INSERT INTO', 'APPEND', 'c'),
(228, 2, 'What does PRIMARY KEY enforce?', 'Uniqueness and non-null values', 'Only uniqueness', 'Only non-null', 'Alphabetical order', 'a'),
(229, 2, 'Which SQL function counts the number of rows?', 'TOTAL()', 'SUM()', 'COUNT()', 'NUM()', 'c'),
(230, 2, 'What does ORDER BY RAND() do?', 'Orders by ID', 'Randomizes result rows', 'Sorts alphabetically', 'Reverses the order', 'b'),
(231, 2, 'Which clause limits the number of result rows?', 'TOP', 'MAX', 'LIMIT', 'REDUCE', 'c'),
(232, 2, 'What is a FOREIGN KEY?', 'A key from another database', 'A reference to another table primary key', 'An encrypted key', 'A duplicate key', 'b'),
(233, 2, 'Which clause filters results after GROUP BY?', 'WHERE', 'FILTER', 'HAVING', 'ORDER BY', 'c'),
(234, 2, 'What does the AVG() function return?', 'The largest value', 'The smallest value', 'The average value', 'The total value', 'c'),
(235, 2, 'Which SQL command removes a table from the database?', 'DELETE TABLE', 'REMOVE TABLE', 'DROP TABLE', 'ERASE TABLE', 'c'),
(236, 2, 'What is normalization in databases?', 'Making queries faster', 'Organizing data to reduce redundancy', 'Encrypting data', 'Sorting rows', 'b'),
(237, 2, 'Which SQL statement modifies existing records?', 'MODIFY', 'CHANGE', 'UPDATE', 'ALTER', 'c'),
(238, 2, 'What does EXPLAIN do in MySQL?', 'Creates a table', 'Shows query execution plan', 'Exports the database', 'Lists all users', 'b'),
(239, 2, 'Which data type stores variable-length strings in MySQL?', 'CHAR', 'TEXT', 'VARCHAR', 'STRING', 'c'),
(240, 2, 'What is a VIEW in MySQL?', 'A backup of a table', 'A saved SQL query acting as a virtual table', 'A user account', 'A stored procedure', 'b'),
(241, 2, 'What does INNER JOIN return?', 'All rows from left table', 'All rows from right table', 'Only matching rows from both tables', 'All rows from both tables', 'c'),
(242, 2, 'Which command saves a transaction permanently in MySQL?', 'SAVE', 'APPLY', 'COMMIT', 'CONFIRM', 'c'),
(243, 3, 'What does JVM stand for?', 'Java Virtual Memory', 'Java Virtual Machine', 'Java Variable Method', 'Java Verified Module', 'b'),
(244, 3, 'Which keyword defines a class in Java?', 'define', 'struct', 'class', 'object', 'c'),
(245, 3, 'How do you print output in Java?', 'print(\"Hello\")', 'console.log(\"Hello\")', 'System.out.println(\"Hello\")', 'echo \"Hello\"', 'c'),
(246, 3, 'Which data type stores true or false in Java?', 'int', 'char', 'String', 'boolean', 'd'),
(247, 3, 'What is the default value of an int in Java?', '1', 'null', '0', '-1', 'c'),
(248, 3, 'Which is NOT a primitive data type in Java?', 'int', 'String', 'char', 'boolean', 'b'),
(249, 3, 'Which keyword is used for inheritance in Java?', 'implements', 'extends', 'inherits', 'super', 'b'),
(250, 3, 'What is the size of an int in Java?', '2 bytes', '4 bytes', '8 bytes', '16 bytes', 'b'),
(251, 3, 'Which method is the entry point of a Java program?', 'start()', 'run()', 'main()', 'init()', 'c'),
(252, 3, 'What keyword creates an instance of a class?', 'create', 'new', 'make', 'build', 'b'),
(253, 3, 'What does the \"final\" keyword do to a variable?', 'Deletes it', 'Makes it constant', 'Loops through it', 'Ends the program', 'b'),
(254, 3, 'Which exception is thrown when dividing by zero in Java?', 'NullPointerException', 'IOException', 'ArithmeticException', 'ClassCastException', 'c'),
(255, 3, 'What is encapsulation in Java?', 'Hiding data using access modifiers', 'Inheriting a class', 'Multiple methods with same name', 'Running code repeatedly', 'a'),
(256, 3, 'Which access modifier makes a member accessible everywhere?', 'private', 'protected', 'default', 'public', 'd'),
(257, 3, 'What does \"this\" refer to in Java?', 'The parent class', 'The current object', 'The main method', 'A static variable', 'b'),
(258, 3, 'Which loop guarantees at least one execution in Java?', 'for', 'while', 'do-while', 'foreach', 'c'),
(259, 3, 'What is the parent class of all Java classes?', 'Main', 'Base', 'Object', 'Super', 'c'),
(260, 3, 'What does \"static\" mean in a Java method?', 'Variable changes each run', 'Belongs to the class not an instance', 'The method is private', 'Class cannot be used', 'b'),
(261, 3, 'What is an interface in Java?', 'A class with all concrete methods', 'A blueprint with abstract methods', 'A data type', 'A loop structure', 'b'),
(262, 3, 'Which method converts a String to uppercase in Java?', 'toUpper()', 'uppercase()', 'toUpperCase()', 'upper()', 'c'),
(263, 4, 'What function prints output in Python?', 'echo()', 'console.log()', 'print()', 'System.out.println()', 'c'),
(264, 4, 'Which symbol starts a comment in Python?', '//', '/*', '#', '--', 'c'),
(265, 4, 'What data type is: x = [1, 2, 3] in Python?', 'tuple', 'dict', 'set', 'list', 'd'),
(266, 4, 'How do you define a function in Python?', 'function myFunc():', 'def myFunc():', 'func myFunc():', 'define myFunc():', 'b'),
(267, 4, 'What does len() return in Python?', 'The last element', 'The length of an object', 'A loop count', 'A longer string', 'b'),
(268, 4, 'What is the result of 10 // 3 in Python?', '3.33', '3', '4', '1', 'b'),
(269, 4, 'Which data type is immutable in Python?', 'list', 'dict', 'tuple', 'set', 'c'),
(270, 4, 'How do you import a module in Python?', 'include math', 'require math', 'import math', 'using math', 'c'),
(271, 4, 'What keyword exits a loop early in Python?', 'exit', 'stop', 'break', 'end', 'c'),
(272, 4, 'What does range(1, 5) produce in Python?', '[1,2,3,4,5]', '[1,2,3,4]', '[0,1,2,3,4]', '[1,5]', 'b'),
(273, 4, 'What is the type of 3.14 in Python?', 'int', 'str', 'float', 'double', 'c'),
(274, 4, 'Which method adds an item to the end of a list?', 'insert()', 'push()', 'add()', 'append()', 'd'),
(275, 4, 'What does \"self\" refer to in a Python class?', 'The parent class', 'The current instance', 'A static variable', 'The module', 'b'),
(276, 4, 'How do you inherit a class in Python?', 'class Dog inherits Animal:', 'class Dog extends Animal:', 'class Dog(Animal):', 'class Dog::Animal:', 'c'),
(277, 4, 'What is the output of 2 ** 4 in Python?', '6', '8', '16', '12', 'c'),
(278, 4, 'Which function converts a string to an integer?', 'toInt()', 'parseInt()', 'int()', 'integer()', 'c'),
(279, 4, 'What does \"pass\" do in Python?', 'Exits a function', 'Does nothing — placeholder', 'Passes a value', 'Skips to next loop', 'b'),
(280, 4, 'Which method removes whitespace from both ends of a string?', 'strip()', 'trim()', 'clean()', 'remove()', 'a'),
(281, 4, 'Which keyword skips the rest of a loop iteration in Python?', 'skip', 'pass', 'continue', 'next', 'c'),
(282, 4, 'Which built-in function returns the largest value in a list?', 'largest()', 'top()', 'max()', 'highest()', 'c'),
(283, 5, 'Which header is needed for cout in C++?', '<stdio.h>', '<iostream>', '<conio.h>', '<string>', 'b'),
(284, 5, 'What is the correct syntax to output text in C++?', 'print(\"Hello\")', 'System.out.println(\"Hello\")', 'cout << \"Hello\";', 'printf Hello', 'c'),
(285, 5, 'Which symbol declares a pointer in C++?', '&', '@', '*', '#', 'c'),
(286, 5, 'What does & do when used with a variable in C++?', 'Dereferences it', 'Gets its memory address', 'Multiplies it', 'Makes it constant', 'b'),
(287, 5, 'What is the size of int in C++ on most systems?', '1 byte', '2 bytes', '4 bytes', '8 bytes', 'c'),
(288, 5, 'What keyword defines a class in C++?', 'struct', 'object', 'class', 'define', 'c'),
(289, 5, 'What is the return type of main() in C++?', 'void', 'string', 'int', 'bool', 'c'),
(290, 5, 'What does the :: operator do in C++?', 'Dereferences a pointer', 'Accesses a scope or namespace', 'Compares values', 'Declares a variable', 'b'),
(291, 5, 'What is a constructor in C++?', 'Destroys an object', 'Special method called when object is created', 'A static function', 'A pointer function', 'b'),
(292, 5, 'What does cin do in C++?', 'Outputs to console', 'Reads user input', 'Clears the screen', 'Includes a file', 'b'),
(293, 5, 'What is function overloading in C++?', 'Same name different parameters', 'Calling a function twice', 'A void function', 'Hiding a function', 'a'),
(294, 5, 'What does endl do in C++?', 'Ends the program', 'Inserts newline and flushes buffer', 'Ends a loop', 'Deletes a variable', 'b'),
(295, 5, 'Which access specifier restricts access to within the class only?', 'public', 'protected', 'private', 'internal', 'c'),
(296, 5, 'What does STL stand for in C++?', 'Standard Template Library', 'String Type Library', 'Static Template Loop', 'System Template Language', 'a'),
(297, 5, 'Which STL container stores key-value pairs?', 'vector', 'list', 'map', 'stack', 'c'),
(298, 5, 'What does \"new\" do in C++?', 'Declares a variable', 'Allocates memory on the heap', 'Creates a loop', 'Imports a module', 'b'),
(299, 5, 'What is a virtual function in C++?', 'A function with no body', 'Can be overridden in derived classes', 'A function inside a struct', 'A static function', 'b'),
(300, 5, 'Which operator accesses members through a pointer in C++?', '.', '::', '->', '&', 'c'),
(301, 5, 'What does \"const\" do to a variable in C++?', 'Makes it changeable', 'Makes it constant', 'Converts its type', 'Clears its memory', 'b'),
(302, 5, 'What is a vector in C++?', 'A fixed-size array', 'A resizable dynamic array from STL', 'A map container', 'A function template', 'b');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `taken_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_results`
--

INSERT INTO `quiz_results` (`id`, `user_id`, `course_id`, `score`, `total`, `taken_at`) VALUES
(1, 2, 1, 7, 10, '2026-04-11 04:35:09'),
(2, 3, 1, 7, 10, '2026-04-11 06:56:08'),
(3, 3, 1, 8, 10, '2026-04-11 07:02:14'),
(4, 3, 2, 3, 10, '2026-04-11 07:05:05'),
(5, 3, 1, 7, 10, '2026-04-11 07:13:49'),
(6, 3, 1, 8, 10, '2026-04-11 12:29:34'),
(7, 2, 1, 11, 20, '2026-04-13 01:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(2, 'raven teel', 'raven08@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2026-04-11 04:32:52'),
(3, 'Irish Fainzan', 'irish@gmail.com', 'fcea920f7412b5da7be0cf42b8c93759', '2026-04-11 04:42:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_results_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
