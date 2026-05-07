@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Yearly Pest Analysis (Mockup)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Pest Type</th>
                                    <th>Total Reports</th>
                                    <th>Avg. Severity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2025</td>
                                    <td>Cecid Fly</td>
                                    <td>120</td>
                                    <td>High</td>
                                </tr>
                                <tr>
                                    <td>2025</td>
                                    <td>Fruit Fly</td>
                                    <td>95</td>
                                    <td>Medium</td>
                                </tr>
                                <tr>
                                    <td>2025</td>
                                    <td>Leaf Hopper</td>
                                    <td>60</td>
                                    <td>Low</td>
                                </tr>
                                <tr>
                                    <td>2024</td>
                                    <td>Cecid Fly</td>
                                    <td>110</td>
                                    <td>Medium</td>
                                </tr>
                                <tr>
                                    <td>2024</td>
                                    <td>Fruit Fly</td>
                                    <td>80</td>
                                    <td>Low</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Monthly Pest Analysis (Mockup)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Pest Type</th>
                                    <th>Total Reports</th>
                                    <th>Avg. Severity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>May 01 2025</td>
                                    <td>Cecid Fly</td>
                                    <td>15</td>
                                    <td>High</td>
                                </tr>
                                <tr>
                                    <td>May 02 2025</td>
                                    <td>Fruit Fly</td>
                                    <td>10</td>
                                    <td>Medium</td>
                                </tr>
                                <tr>
                                    <td>May 03 2025</td>
                                    <td>Leaf Hopper</td>
                                    <td>7</td>
                                    <td>Low</td>
                                </tr>
                                <tr>
                                    <td>May 04 2025</td>
                                    <td>Cecid Fly</td>
                                    <td>12</td>
                                    <td>Medium</td>
                                </tr>
                                <tr>
                                    <td>May 05 2025</td>
                                    <td>Fruit Fly</td>
                                    <td>8</td>
                                    <td>Low</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 