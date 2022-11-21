@extends('app')

@section('content')
    <!-- Main content -->
    <section class="content container">
    @include('layouts.errors-and-messages')
    <!-- Default box -->
        @if($addresses)
        
        <div class="container">
            <div class="row my-4">
                <div class="col-12">
                    <h4 class="mb-3">My addresses</h4>
                </div>
                <div class="col-12">
                    <table class="table table-striped border">
                        <tbody>
                            <tr>
                                <th>Alias</th>
                                <th>Address 1</th>
                                <th>Country</th>
                                <th>Province</th>
                                <th>City</th>
                                <th>Zip Code</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </tbody>
                        <tbody>
                        @foreach ($addresses as $address)
                            <tr>
                                <td>{{ $address->alias }}</td>
                                <td>{{ $address->address_1 }}</td>
                                <td>
                                    @if( !is_null($address->country) )  
                                        {{ $address->country->name }} 
                                    @endif
                                </td>
                                <td>
                                    @if( !is_null($address->province) )
                                        {{ $address->province->name }}</td>
                                    @else
                                        --
                                    @endif
                                <td>
                                    @if( is_null($address->city) )
                                        {{ $address->city }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>{{ $address->zipcode }}</td>
                                <td>
                                    <form action="{{-- route('customer.address.destroy', $customer->id, $address->id) --}}" method="post" class="form-horizontal">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="delete">
                                        <div class="btn-group">
                                            <a href="{{-- route('admin.addresses.edit', $address->id) --}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                            <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @else
            <div class="box">
                <div class="box-body"><p class="alert alert-warning">No addresses found.</p></div>
            </div>
        @endif
    </section>
    <!-- /.content -->
@endsection