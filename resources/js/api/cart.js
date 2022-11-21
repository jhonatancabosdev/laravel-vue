import axios from 'axios';

const client = axios.create({
  baseURL: '/',
});

export default {
    all(params) {
        return client.get('cart', params);
    },
    find(id) {
        return client.get(`cart${id}`);
    },
    add(data){
        return client.post(`cart`, data);
    },
    update(id, data) {
        return client.put(`cart/${id}`, data);
    },
    delete(id) {
        return client.delete(`cart/${id}`);
    },
    getCouponCode() {
        return client.get(`cart/couponcode/show`);
    },
    applyCouponCode(code) {
        return client.get(`cart/couponcode/${code}`);
    }
};