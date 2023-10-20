import React, { Component } from 'react'
import { connect } from 'react-redux';
import CountUp from 'react-countup'
class Report extends Component {

    render() {
        const { fixNavbar } = this.props;
        return (
            <>
                <div>
                    <div className={`section-body ${fixNavbar ? "marginTop" : ""}`}>
                        <div className="container-fluid">
                            <div className="d-flex justify-content-between align-items-center">
                                <ul className="nav nav-tabs page-header-tab">
                                    <li className="nav-item"><a className="nav-link active" id="Report-tab" data-toggle="tab" href="#Report-Invoices">Expenses</a></li>
                                    <li className="nav-item"><a className="nav-link" id="Report-tab" data-toggle="tab" href="#Report-Invoice">Invoice</a></li>
                                    <li className="nav-item"><a className="nav-link" id="Report-tab" data-toggle="tab" href="#Report-detail">Detail</a></li>
                                </ul>
                                <div className="header-action d-md-flex">
                                    <select className="custom-select mr-2">
                                        <option>Year</option>
                                        <option>Month</option>
                                        <option>Week</option>
                                    </select>
                                    <button type="button" className="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i className="fe fe-plus mr-2" />Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="section-body mt-3">
                        <div className="container-fluid">
                            <div className="tab-content mt-3">
                                <div className="tab-pane fade show active" id="Report-Invoices" role="tabpanel">
                                    <div className="row">
                                        <div className="col-lg-3 col-md-6">
                                            <div className="card">
                                                <div className="card-body">
                                                    <div className="card-value float-right text-azure">23%</div>
                                                    <h4 className="mb-1">$<span className="counter"><CountUp end={552} /></span></h4>
                                                    <div>Computer</div>
                                                    <div className="mt-4">
                                                        <div className="progress progress-xs">
                                                            <div className="progress-bar bg-azure" style={{ width: '23%' }} />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-lg-3 col-md-6">
                                            <div className="card">
                                                <div className="card-body">
                                                    <div className="card-value float-right text-cyan">52%</div>
                                                    <h4 className="mb-1">$<span className="counter"><CountUp end={2450} /></span></h4>
                                                    <div>Laptop</div>
                                                    <div className="mt-4">
                                                        <div className="progress progress-xs">
                                                            <div className="progress-bar bg-cyan" style={{ width: '52%' }} />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-lg-3 col-md-6">
                                            <div className="card">
                                                <div className="card-body">
                                                    <div className="card-value float-right text-danger">27%</div>
                                                    <h4 className="mb-1">$<span className="counter"><CountUp end={1258} /></span></h4>
                                                    <div>Accessories</div>
                                                    <div className="mt-4">
                                                        <div className="progress progress-xs">
                                                            <div className="progress-bar bg-danger" style={{ width: '27%' }} />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-lg-3 col-md-6">
                                            <div className="card">
                                                <div className="card-body">
                                                    <div className="card-value float-right text-pink">12%</div>
                                                    <h4 className="mb-1">$<span className="counter"><CountUp end={562} /></span></h4>
                                                    <div>Others</div>
                                                    <div className="mt-4">
                                                        <div className="progress progress-xs">
                                                            <div className="progress-bar bg-pink" style={{ width: '12%' }} />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="card">
                                        <div className="card-body">
                                            <div className="table-responsive">
                                                <table className="table table-hover table-striped table-vcenter mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Order by</th>
                                                            <th>From</th>
                                                            <th>Date</th>
                                                            <th>Paid By</th>
                                                            <th>Status</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>HP Laptop</td>
                                                            <td>Marshall Nichols</td>
                                                            <td>Paytem</td>
                                                            <td>07 March, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-warning">Pending</span></td>
                                                            <td>$205</td>
                                                        </tr>
                                                        <tr>
                                                            <td>iMack Desktop</td>
                                                            <td>Marshall Nichols</td>
                                                            <td>ebay USA</td>
                                                            <td>22 July, 2017</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-warning">Pending</span></td>
                                                            <td>$355</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Logitech USB Mouse, Keyboard</td>
                                                            <td>Marshall Nichols</td>
                                                            <td>Amazon</td>
                                                            <td>28 July, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$40</td>
                                                        </tr>
                                                        <tr>
                                                            <td>MacBook Pro Air</td>
                                                            <td>Debra Stewart</td>
                                                            <td>Amazon</td>
                                                            <td>17 Jun, 2018</td>
                                                            <td><i className="payment payment-mastercard" data-toggle="tooltip" data-original-title="payment payment-mastercard" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$800</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Dell Monitor 28 inch</td>
                                                            <td>Ava Alexander</td>
                                                            <td>Flipkart UK</td>
                                                            <td>21 Jun, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$205</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Logitech USB Mouse, Keyboard</td>
                                                            <td>Marshall Nichols</td>
                                                            <td>Amazon</td>
                                                            <td>28 July, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$40</td>
                                                        </tr>
                                                        <tr>
                                                            <td>MacBook Pro Air</td>
                                                            <td>Debra Stewart</td>
                                                            <td>Amazon</td>
                                                            <td>17 Jun, 2018</td>
                                                            <td><i className="payment payment-mastercard" data-toggle="tooltip" data-original-title="payment payment-mastercard" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$800</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="tab-pane fade" id="Report-Invoice" role="tabpanel">
                                    <div className="card">
                                        <div className="card-body">
                                            <div className="row text-center">
                                                <div className="col-lg-4">
                                                    <h4 className="mb-1"><i className="mdi mdi-trending-down text-danger" /> <span className="counter">562</span></h4>
                                                    <div className="text-muted-dark">Total Approved</div>
                                                </div>
                                                <div className="col-lg-4">
                                                    <h4 className="mb-1"><i className="mdi mdi-trending-up text-success" /> <span className="counter">254</span></h4>
                                                    <div className="text-muted-dark">Pending Invoice</div>
                                                </div>
                                                <div className="col-lg-4">
                                                    <h4 className="mb-1"><i className="mdi mdi-trending-neutral text-warning" /> <span className="counter">982</span></h4>
                                                    <div className="text-muted-dark">Closed</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="card">
                                        <div className="card-header">
                                            <h3 className="card-title">Invoices</h3>
                                            <div className="card-options">
                                                <form>
                                                    <div className="input-group">
                                                        <input type="text" className="form-control form-control-sm" placeholder="Search something..." name="s" />
                                                        <span className="input-group-btn ml-2"><button className="btn btn-icon btn-sm" type="submit"><span className="fe fe-search" /></button></span>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div className="card-body">
                                            <div className="table-responsive">
                                                <table className="table table-hover table-striped table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th>Invoice No.</th>
                                                            <th>Clients</th>
                                                            <th>Date</th>
                                                            <th>Type</th>
                                                            <th>Status</th>
                                                            <th className="w100">Amount</th>
                                                            <th className="w150">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>#LA-5218</td>
                                                            <td>vPro tec LLC.</td>
                                                            <td>07 March, 2018</td>
                                                            <td><i className="payment payment-cirrus" data-toggle="tooltip" data-original-title="payment payment-cirrus" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$4,205</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-1212</td>
                                                            <td>BT Technology</td>
                                                            <td>25 Jun, 2018</td>
                                                            <td><i className="payment payment-bitcoin" data-toggle="tooltip" data-original-title="payment payment-bitcoin" /></td>
                                                            <td><span className="badge badge-warning">Pending</span></td>
                                                            <td>$5,205</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-0222</td>
                                                            <td>More Infoweb Pvt.</td>
                                                            <td>12 July, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-warning">Pending</span></td>
                                                            <td>$2,000</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-0312</td>
                                                            <td>RETO Tech LLC.</td>
                                                            <td>13 July, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$10,000</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-5656</td>
                                                            <td>SDRAPP Pvt.</td>
                                                            <td>27 July, 2018</td>
                                                            <td><i className="payment payment-visa-dark" data-toggle="tooltip" data-original-title="payment payment-visa-dark" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$1,205</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-4515</td>
                                                            <td>Kdash Infoweb LLC.</td>
                                                            <td>07 March, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$4,205</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-1212</td>
                                                            <td>BT Technology</td>
                                                            <td>25 Jun, 2018</td>
                                                            <td><i className="payment payment-bitcoin" data-toggle="tooltip" data-original-title="payment payment-bitcoin" /></td>
                                                            <td><span className="badge badge-warning">Pending</span></td>
                                                            <td>$5,205</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-0222</td>
                                                            <td>More Infoweb Pvt.</td>
                                                            <td>12 July, 2018</td>
                                                            <td><i className="payment payment-paypal" data-toggle="tooltip" data-original-title="payment payment-paypal" /></td>
                                                            <td><span className="badge badge-warning">Pending</span></td>
                                                            <td>$2,000</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-7845</td>
                                                            <td>BT infoweb</td>
                                                            <td>25 Jun, 2018</td>
                                                            <td><i className="payment payment-bitcoin" data-toggle="tooltip" data-original-title="payment payment-bitcoin" /></td>
                                                            <td><span className="badge badge-warning">Pending</span></td>
                                                            <td>$5,205</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>#LA-5656</td>
                                                            <td>SDRAPP Pvt.</td>
                                                            <td>27 July, 2018</td>
                                                            <td><i className="payment payment-visa-dark" data-toggle="tooltip" data-original-title="payment payment-visa-dark" /></td>
                                                            <td><span className="badge badge-success">Approved</span></td>
                                                            <td>$1,205</td>
                                                            <td>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Send Invoice" data-toggle="tooltip" data-placement="top"><i className="icon-envelope text-info" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Print" data-toggle="tooltip" data-placement="top"><i className="icon-printer" /></button>
                                                                <button type="button" className="btn btn-icon btn-sm" title="Delete" data-toggle="tooltip" data-placement="top"><i className="icon-trash text-danger" /></button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <nav aria-label="Page navigation">
                                                <ul className="pagination mb-0 justify-content-end">
                                                    <li className="page-item"><a className="page-link" href="/#">Previous</a></li>
                                                    <li className="page-item active"><a className="page-link" href="/#">1</a></li>
                                                    <li className="page-item"><a className="page-link" href="/#">2</a></li>
                                                    <li className="page-item"><a className="page-link" href="/#">3</a></li>
                                                    <li className="page-item"><a className="page-link" href="/#">Next</a></li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                                <div className="tab-pane fade" id="Report-detail" role="tabpanel">
                                    <div className="row clearfix">
                                        <div className="col-12">
                                            <div className="card">
                                                <div className="card-header">
                                                    <h3 className="card-title">#AB0017</h3>
                                                    <div className="card-options">
                                                        <button type="button" className="btn btn-primary"><i className="si si-printer" /> Print Invoice</button>
                                                    </div>
                                                </div>
                                                <div className="card-body">
                                                    <div className="row my-8">
                                                        <div className="col-6">
                                                            <p className="h3">Company</p>
                                                            <address>
                                                                Street Address<br />
                                                                State, City<br />
                                                                Region, Postal Code<br />
                                                                ltd@example.com
                      </address>
                                                        </div>
                                                        <div className="col-6 text-right">
                                                            <p className="h3">Client</p>
                                                            <address>
                                                                Street Address<br />
                                                                State, City<br />
                                                                Region, Postal Code<br />
                                                                ctr@example.com
                      </address>
                                                        </div>
                                                    </div>
                                                    <div className="table-responsive push">
                                                        <table className="table table-bordered table-hover">
                                                            <tbody><tr>
                                                                <th className="text-center width35" />
                                                                <th>Product</th>
                                                                <th className="text-center" style={{ width: '1%' }}>Qnt</th>
                                                                <th className="text-right" style={{ width: '1%' }}>Unit</th>
                                                                <th className="text-right" style={{ width: '1%' }}>Amount</th>
                                                            </tr>
                                                                <tr>
                                                                    <td className="text-center">1</td>
                                                                    <td>
                                                                        <p className="font600 mb-1">Logo Creation</p>
                                                                        <div className="text-muted">Logo and business cards design</div>
                                                                    </td>
                                                                    <td className="text-center">1</td>
                                                                    <td className="text-right">$1.800,00</td>
                                                                    <td className="text-right">$1.800,00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td className="text-center">2</td>
                                                                    <td>
                                                                        <p className="font600 mb-1">Online Store Design &amp; Development</p>
                                                                        <div className="text-muted">Design/Development for all popular modern browsers</div>
                                                                    </td>
                                                                    <td className="text-center">1</td>
                                                                    <td className="text-right">$20.000,00</td>
                                                                    <td className="text-right">$20.000,00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td className="text-center">3</td>
                                                                    <td>
                                                                        <p className="font600 mb-1">App Design</p>
                                                                        <div className="text-muted">Promotional mobile application</div>
                                                                    </td>
                                                                    <td className="text-center">1</td>
                                                                    <td className="text-right">$3.200,00</td>
                                                                    <td className="text-right">$3.200,00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colSpan={4} className="font600 text-right">Subtotal</td>
                                                                    <td className="text-right">$25.000,00</td>
                                                                </tr>
                                                                <tr className="bg-light">
                                                                    <td colSpan={4} className="font600 text-right">Vat Rate</td>
                                                                    <td className="text-right">20%</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colSpan={4} className="font600 text-right">Vat Due</td>
                                                                    <td className="text-right">$5.000,00</td>
                                                                </tr>
                                                                <tr className="bg-green text-light">
                                                                    <td colSpan={4} className="font700 text-right">Total Due</td>
                                                                    <td className="font700 text-right">$30.000,00</td>
                                                                </tr>
                                                            </tbody></table>
                                                    </div>
                                                    <p className="text-muted text-center">Thank you very much for doing business with us. We look forward to working with you again!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </>
        )
    }
}
const mapStateToProps = state => ({
    fixNavbar: state.settings.isFixNavbar
})

const mapDispatchToProps = dispatch => ({})
export default connect(mapStateToProps, mapDispatchToProps)(Report);