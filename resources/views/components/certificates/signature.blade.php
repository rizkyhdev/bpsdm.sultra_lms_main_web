@props(['name', 'title'])

<div class="signature-block">
    <div class="signature-line"></div>
    <div class="signature-name">{{ $name }}</div>
    <div class="signature-title">{{ $title }}</div>
</div>

<style>
    .signature-block {
        text-align: center;
        width: 200px;
    }
    
    .signature-line {
        border-top: 2px solid #333;
        margin: 60px auto 10px;
        width: 150px;
    }
    
    .signature-name {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    
    .signature-title {
        font-size: 14px;
        color: #666;
        margin-top: 5px;
    }
</style>

