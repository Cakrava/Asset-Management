<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Sesuaikan otorisasi sesuai kebutuhan aplikasi Anda.
        // Misalnya, Anda bisa memeriksa apakah pengguna memiliki peran 'admin' atau 'operator'.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transaction_id' => 'required|string|max:255',
            'flow_type' => 'required|in:in,out',
            'is_other_source_client' => 'required|in:0,1',
            
            // Validasi data klien
            'client_data.name' => 'required_if:is_other_source_client,1|nullable|string|max:255',
            'client_data.phone' => 'nullable|string|max:255',
            'client_data.institution' => 'nullable|string|max:255',
            'client_data.institution_type' => 'nullable|string|max:255',
            'client_data.id' => 'required_if:is_other_source_client,0|nullable|exists:users,id', // Untuk klien yang sudah ada, ID harus ada di tabel users

            // Validasi keranjang transaksi
            'transaction_cart' => 'required|array|min:1',
            'transaction_cart.*.id' => 'required|numeric', // Ini bisa jadi device_id (master) atau stored_device_id
            'transaction_cart.*.name' => 'required|string',
            'transaction_cart.*.condition' => 'required|string',
            'transaction_cart.*.quantity' => 'required|integer|min:1',
            'transaction_cart.*.source' => 'required|in:manual,deployed,letter',
            
            // Aturan kondisional untuk sumber 'deployed'
            'transaction_cart.*.originalStoredDeviceId' => 'required_if:transaction_cart.*.source,deployed|numeric',
            'transaction_cart.*.deploymentDetailId' => 'required_if:transaction_cart.*.source,deployed|numeric|exists:deployment_device_details,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'transaction_cart.required' => 'Keranjang transaksi tidak boleh kosong.',
            'transaction_cart.min' => 'Keranjang transaksi harus memiliki setidaknya satu item.',
            'transaction_cart.*.quantity.min' => 'Jumlah perangkat minimal 1.',
            'client_data.name.required_if' => 'Nama klien harus diisi jika sumber lain diaktifkan.',
            'client_data.id.required_if' => 'Klien harus dipilih jika sumber lain dinonaktifkan.',
            'client_data.id.exists' => 'Klien yang dipilih tidak valid.',
            'transaction_cart.*.deploymentDetailId.required_if' => 'ID detail deployment diperlukan untuk perangkat yang terpasang.',
            'transaction_cart.*.deploymentDetailId.exists' => 'Detail deployment tidak ditemukan atau tidak valid.',
        ];
    }
}