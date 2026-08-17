@extends('layouts.app')

@section('content')
<div class="admin-container">
    <h1 class="admin-title">💬 Live Chat Pelanggan</h1>

    <div class="card">
        @if($sessions->isEmpty())
            <p style="text-align:center; color:#666;">Belum ada percakapan dari pelanggan.</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Pelanggan (Sesi)</th>
                        <th>Pesan Terakhir</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr>
                        <td>
                            <strong>{{ substr($session->session_id, 0, 8) }}...</strong>
                        </td>
                        <td>
                            {{ Str::limit($session->last_message->message ?? '', 50) }}
                        </td>
                        <td>
                            {{ $session->last_message ? $session->last_message->created_at->diffForHumans() : '-' }}
                        </td>
                        <td>
                            @if($session->unread_count > 0)
                                <span class="badge-danger">{{ $session->unread_count }} Pesan Baru</span>
                            @else
                                <span class="badge-success">Terbaca</span>
                            @endif
                        </td>
                        <td>
                            <a href="/admin/chat/{{ $session->session_id }}" class="btn-edit" style="background:#2196F3;">Buka Obrolan</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
