<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemberitahuan Status Lamaran</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px;">
    <h2>Halo {{ $teamMember->nama }},</h2>
    
    <p>Terima kasih atas ketertarikan Anda untuk bergabung dengan tim <strong>Indo Berkah Konstruksi</strong>.</p>
    
    <p>Setelah meninjau profil dan kualifikasi Anda, dengan berat hati kami sampaikan bahwa saat ini kami <strong>belum dapat menerima</strong> permohonan Anda untuk bergabung bersama kami.</p>
    
    @if($teamMember->rejection_reason)
    <div style="background-color: #f9f9f9; border-left: 4px solid #C5A880; padding: 15px; margin: 20px 0;">
        <p style="margin: 0;"><strong>Alasan/Catatan:</strong></p>
        <p style="margin: 5px 0 0 0; color: #555;">{{ $teamMember->rejection_reason }}</p>
    </div>
    @endif
    
    <p>Kami sangat menghargai waktu dan usaha yang Anda luangkan. Kami akan menyimpan profil Anda dalam database kami untuk kesempatan di masa mendatang yang mungkin lebih sesuai dengan kualifikasi Anda.</p>
    
    <p>Semoga Anda sukses dalam perjalanan karir Anda selanjutnya.</p>
    
    <br>
    <p>Salam Hormat,</p>
    <p><strong>Tim Indo Berkah Konstruksi</strong></p>
</body>
</html>
