import paymentApi from "../api/paymentApi";

class PaymentService {
  async createPayment(data) {
    const response = await paymentApi.create(data);

    return response.data;
  }

  async getPayments(params = {}) {
    const response = await paymentApi.getAll(params);

    return response.data;
  }

  async getPayment(id) {
    const response = await paymentApi.getById(id);

    return response.data;
  }

  async updatePayment(id, data) {
    const response = await paymentApi.update(id, data);

    return response.data;
  }

  async deletePayment(id) {
    const response = await paymentApi.delete(id);

    return response.data;
  }

  async getInvoices() {
    const response = await paymentApi.getInvoices();

    return response.data;
  }

  async getInvoice(id) {
    const response = await paymentApi.getInvoice(id);

    return response.data;
  }

  async downloadInvoice(id) {
    return paymentApi.downloadInvoice(id);
  }

  async getRefunds() {
    const response = await paymentApi.getRefunds();

    return response.data;
  }

  async createRefund(data) {
    const response = await paymentApi.createRefund(data);

    return response.data;
  }

  async approveRefund(id) {
    const response = await paymentApi.approveRefund(id);

    return response.data;
  }

  async processRefund(id) {
    const response = await paymentApi.processRefund(id);

    return response.data;
  }

  /**
   * Redirect patient to Chapa
   */
  redirectToCheckout(checkoutUrl) {
    window.location.href = checkoutUrl;
  }
}

export default new PaymentService();