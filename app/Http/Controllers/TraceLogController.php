<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TraceLogController extends Controller
{

    public function show($modelName, $modelId)
    {
        $data = $users = DB::table('trace_log as l')->join('trace_log_values as v', 'l.id', '=', 'v.trace_log_id')
            ->select('l.id', 'l.user', 'l.date', 'v.column', 'v.from', 'v.to')
            ->where([
            'l.model' => $modelName,
            'l.model_id' => $modelId
        ])
            ->orderby('date', 'desc')
            ->get();
        $result = [];
        foreach ($data as $row) {
            $row = (array) $row;
            if(!isset($result[skDateTime($row['date']) . ' ' . $row['user']])) {
                $result[skDateTime($row['date']) . ' ' . $row['user']] = [];
            }
            $result[skDateTime($row['date']) . ' ' . $row['user']][] = [
                'column' => $row['column'],
                'from' => $row['from'],
                'to' => $row['to']
            ];
        }
        echo "<button onclick=\"$('#traceLog').html('');\">X</button><table  class=\"border_table table table-striped\">
            <tr>
                <th>".__("When & who")."</th>
                <th>".__("Column")."</th>
                <th>".__("From")."</th>
                <th>".__("To")."</th>
            </tr>
            ";
        foreach ($result as $dateUser => $data) {
            foreach ($data as $i => $row) {
                $row = (array) $row;
                echo "<tr>
                    " . ($i == 0 ? "<td rowspan='" . count($result[$dateUser]) . "'>{$dateUser}</td>" : '') . "<td>".__($row['column'])."</td>
                    <td>".__($row['from'])."</td>
                    <td>".__($row['to'])."</td>
                </tr>";
            }
        }
        
        echo '</table>';
    }
}
