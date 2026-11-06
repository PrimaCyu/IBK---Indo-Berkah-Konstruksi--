<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Selamat Anda Diterima</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px;">
    <h2>Halo {{ $teamMember->nama }},</h2>
    
    <p>Selamat! Kami sangat senang memberitahukan bahwa permohonan Anda untuk bergabung dengan <strong>Indo Berkah Konstruksi</strong> telah kami <strong>TERIMA</strong>.</p>
    
    <div style="background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-size: 16px;"><strong>Tanggal Mulai Bekerja:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 18px; color: #2E7D32; font-weight: bold;">
            {{ \Carbon\Carbon::parse($teamMember->start_date)->translatedFormat('d F Y') }}
        </p>
    </div>
    
    <p>Tim kami sangat terkesan dengan profil Anda dan kami tidak sabar untuk mulai berkolaborasi. Profil Anda juga akan segera ditampilkan di halaman utama "Our Team" website kami.</p>
    
    <p>Jika Anda memiliki pertanyaan lebih lanjut mengenai persiapan hari pertama Anda, jangan ragu untuk membalas email ini.</p>
    
    <br>
    <p>Salam Hangat,</p>
    <p><strong>Tim Indo Berkah Konstruksi</strong></p>
</body>
</html>
