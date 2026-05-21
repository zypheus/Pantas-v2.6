<!DOCTYPE html>
<html>
<head>
    <title>Book Report</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h1 { text-align: center; margin-bottom: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .subheading {
            margin-top: 40px;
            font-size: 16px;
            font-weight: bold;
        }
        .course-section {
            margin-top: 20px;
        }
        .course-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <h1>Library Book Report</h1>
    <h2>Total Number of Books: {{ $totalBooks }}</h2>

    <table>
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Total Copies</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($booksByTitle as $book)
                <tr>
                    <td>{{ $book->title_statement }}</td>
                    <td>{{ $book->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Subjects per Course Section -->
    <h2 class="subheading">Subjects per Course</h2>

    @foreach ($groupedBooks as $course => $subjects)
        <div class="course-section">
            <div class="course-title">{{ $course }}</div>
            <ul>
                @foreach ($subjects as $subject)
                    <li>{{ $subject->title_statement }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach

</body>
</html>
