<?php
    /**
     * CSV ダウンロード処理（Laravel）
     * @param $csvList array 出力するCSV データのリスト
     * @param $header array CSV １行目のヘッダー
     * @param $fileName string 出力ファイル名
     * @return \Illuminate\Http\Response make レスポンスを返す
     */
    function downloadCsv($csvList, $header, $fileName)
    {
        // CSV のヘッダーを設定
        if (count($header) > 0) {
            array_unshift($csvList, $header);
        }
        // 仮ファイルを開く
        $stream = fopen('php://temp', 'r+b');
        foreach ($csvList as $csv) {
            fputcsv($stream, $csv);
        }
        rewind($stream);
        // 改行コードの変換
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        // UTF-8 を SJIS-win へ変換
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename*=UTF-8\'\''.rawurlencode($fileName.'.csv')
        ];
        return response()->make($csv, 200, $headers);
    }