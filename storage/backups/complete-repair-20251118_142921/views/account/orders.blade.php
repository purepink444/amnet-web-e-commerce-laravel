@extends('layouts.default')

@section('title', 'โปรไฟล์ของฉัน - คำสั่งซื้อ')

@section('content')

<div class="bg-gradient-profile py-5">
    <div class="container py-5">
        <div class="row">
            

            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-4">คำสั่งซื้อของฉัน</h4>

                        @if ($orders->isEmpty())
                            <p>คุณยังไม่มีคำสั่งซื้อใดๆ</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>หมายเลขคำสั่งซื้อ</th>
                                        <th>วันที่</th>
                                        <th>สถานะ</th>
                                        <th>ยอดรวม</th>
                                        <th>การกระทำ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $order->status }}</td>
                                            <td>{{ number_format($order->total_amount, 2) }} บาท</td>
                                            <td><a href="{{ route('account.order.show', $order->id) }}" class="btn btn-primary btn-sm">ดูรายละเอียด</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{ $orders->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection