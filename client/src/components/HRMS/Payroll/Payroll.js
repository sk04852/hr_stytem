import React, { Component } from 'react';
import { connect } from 'react-redux';
import CountUp from 'react-countup';
class Payroll extends Component {


	render() {
		const { fixNavbar } = this.props;
		return (
			<>
				<div className={`section-body ${fixNavbar ? "marginTop" : ""}`}>
					<div className="container-fluid">
						<div className="d-flex justify-content-between align-items-center">
							<ul className="nav nav-tabs page-header-tab">
								<li className="nav-item">
									<a
										className="nav-link active"
										id="Payroll-tab"
										data-toggle="tab"
										href="#Payroll-Salary"
									>
										Employee Salary
									</a>
								</li>
								<li className="nav-item">
									<a className="nav-link" id="Payroll-tab" data-toggle="tab" href="#Payroll-Payslip">
										Payslip
									</a>
								</li>
							</ul>
							<div className="header-action">
								<button
									type="button"
									className="btn btn-primary"
									data-toggle="modal"
									data-target="#exampleModal"
								>
									<i className="fe fe-plus mr-2" />
									Add
								</button>
							</div>
						</div>
					</div>
				</div>
				<div className="section-body mt-3">
					<div className="container-fluid">
						<div className="tab-content mt-3">
							<div className="tab-pane fade show active" id="Payroll-Salary" role="tabpanel">
								<div className="row clearfix">
									<div className="col-lg-3 col-md-6">
										<div className="card">
											<div className="card-body">
												<h6>Web Developer</h6>
												<h3 className="pt-3">
													$<span className="counter"><CountUp end={18960} /></span>
												</h3>
												<span>
													<span className="text-danger mr-2">
														<i className="fa fa-long-arrow-down" /> 5.27%
													</span>{' '}
													Since last month
												</span>
											</div>
										</div>
									</div>
									<div className="col-lg-3 col-md-6">
										<div className="card">
											<div className="card-body">
												<h6>App Developer</h6>
												<h3 className="pt-3">
													$<span className="counter"><CountUp end={11783} /></span>
												</h3>
												<span>
													<span className="text-success mr-2">
														<i className="fa fa-long-arrow-up" /> 11.38%
													</span>{' '}
													Since last month
												</span>
											</div>
										</div>
									</div>
									<div className="col-lg-3 col-md-6">
										<div className="card">
											<div className="card-body">
												<h6>Designer</h6>
												<h3 className="pt-3">
													$<span className="counter"><CountUp end={2254} /></span>
												</h3>
												<span>
													<span className="text-success mr-2">
														<i className="fa fa-long-arrow-up" /> 9.61%
													</span>{' '}
													Since last month
												</span>
											</div>
										</div>
									</div>
									<div className="col-lg-3 col-md-6">
										<div className="card">
											<div className="card-body">
												<h6>Marketing</h6>
												<h3 className="pt-3">
													$<span className="counter"><CountUp end={8751} /></span>
												</h3>
												<span>
													<span className="text-danger mr-2">
														<i className="fa fa-long-arrow-down" /> 2.27%
													</span>{' '}
													Since last month
												</span>
											</div>
										</div>
									</div>
								</div>
								<div className="card">
									<div className="card-header">
										<h3 className="card-title">Employee</h3>
										<div className="card-options">
											<form>
												<div className="input-group">
													<input
														type="text"
														className="form-control form-control-sm"
														placeholder="Search something..."
														name="s"
													/>
													<span className="input-group-btn ml-2">
														<button className="btn btn-icon" type="submit">
															<span className="fe fe-search" />
														</button>
													</span>
												</div>
											</form>
										</div>
									</div>
									<div className="card-body">
										<div className="table-responsive">
											<table className="table table-hover table-striped table-vcenter text-nowrap">
												<thead>
													<tr>
														<th style={{ width: 20 }}>#</th>
														<th>Employee</th>
														<th className="w200">Role</th>
														<th className="w60">Salary</th>
														<th className="w60">Status</th>
														<th className="w200">Action</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>
															<span>01</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<span
																	className="avatar avatar-pink"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																>
																	WH
																</span>
																<div className="ml-3">
																	<a href="fake_url">South Shyanne</a>
																	<p className="mb-0">south.shyanne@example.com</p>
																</div>
															</div>
														</td>
														<td>Web Developer</td>
														<td>$1200</td>
														<td>
															<span className="tag tag-success ml-0 mr-0">Done</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>02</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<img
																	className="avatar"
																	src="../assets/images/xs/avatar1.jpg"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																	alt="Avatar"
																/>
																<div className="ml-3">
																	<a href="fake_url">Zoe Baker</a>
																	<p className="mb-0">zoe.baker@example.com</p>
																</div>
															</div>
														</td>
														<td>Graphics Desgber</td>
														<td>$378</td>
														<td>
															<span className="tag tag-success ml-0 mr-0">Done</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>03</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<span
																	className="avatar avatar-blue"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																>
																	WH
																</span>
																<div className="ml-3">
																	<a href="fake_url">Colin Brown</a>
																	<p className="mb-0">colinbrown@example.com</p>
																</div>
															</div>
														</td>
														<td>HTML Developer</td>
														<td>$653</td>
														<td>
															<span className="tag tag-success ml-0 mr-0">Done</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>04</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<span
																	className="avatar avatar-green"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																>
																	WH
																</span>
																<div className="ml-3">
																	<a href="fake_url">Kevin Gill</a>
																	<p className="mb-0">kevin.gill@example.com</p>
																</div>
															</div>
														</td>
														<td>Mobile</td>
														<td>$451</td>
														<td>
															<span className="tag tag-warning  ml-0 mr-0">Panding</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>05</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<img
																	className="avatar"
																	src="../assets/images/xs/avatar2.jpg"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																	alt="Avatar"
																/>
																<div className="ml-3">
																	<a href="fake_url">Brandon Smith</a>
																	<p className="mb-0">Maria.gill@example.com</p>
																</div>
															</div>
														</td>
														<td>VueJs FrontEnd</td>
														<td>$1,989</td>
														<td>
															<span className="tag tag-success  ml-0 mr-0">Done</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>06</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<img
																	className="avatar"
																	src="../assets/images/xs/avatar3.jpg"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																	alt="Avatar"
																/>
																<div className="ml-3">
																	<a href="fake_url">Kevin Baker</a>
																	<p className="mb-0">kevin.baker@example.com</p>
																</div>
															</div>
														</td>
														<td>Java Developer</td>
														<td>$343</td>
														<td>
															<span className="tag tag-warning  ml-0 mr-0">Panding</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>07</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<img
																	className="avatar"
																	src="../assets/images/xs/avatar4.jpg"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																	alt="Avatar"
																/>
																<div className="ml-3">
																	<a href="fake_url">Colin Brown</a>
																	<p className="mb-0">colin-brown@example.com</p>
																</div>
															</div>
														</td>
														<td>Designer</td>
														<td>$653</td>
														<td>
															<span className="tag tag-success ml-0 mr-0">Done</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>08</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<img
																	className="avatar"
																	src="../assets/images/xs/avatar5.jpg"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																	alt="Avatar"
																/>
																<div className="ml-3">
																	<a href="fake_url">Kevin Gill</a>
																	<p className="mb-0">kevin-gill@example.com</p>
																</div>
															</div>
														</td>
														<td>Team Leader</td>
														<td>$451</td>
														<td>
															<span className="tag tag-warning  ml-0 mr-0">Panding</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>09</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<span
																	className="avatar avatar-green"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																>
																	WH
																</span>
																<div className="ml-3">
																	<a href="fake_url">Kevin Gill</a>
																	<p className="mb-0">kevin.gill@example.com</p>
																</div>
															</div>
														</td>
														<td>Mobile</td>
														<td>$451</td>
														<td>
															<span className="tag tag-warning  ml-0 mr-0">Panding</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
													<tr>
														<td>
															<span>10</span>
														</td>
														<td>
															<div className="d-flex align-items-center">
																<img
																	className="avatar"
																	src="../assets/images/xs/avatar7.jpg"
																	data-toggle="tooltip"
																	data-placement="top"
																	title="Avatar Name"
																	alt="Avatar"
																/>
																<div className="ml-3">
																	<a href="fake_url">Brandon Smith</a>
																	<p className="mb-0">Maria.gill@example.com</p>
																</div>
															</div>
														</td>
														<td>VueJs FrontEnd</td>
														<td>$1,989</td>
														<td>
															<span className="tag tag-success  ml-0 mr-0">Done</span>
														</td>
														<td>
															<button
																type="button"
																className="btn btn-icon"
																title="Send Invoice"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-envelope text-info" />
															</button>
															<button
																type="button"
																className="btn btn-icon "
																title="Print"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-printer" />
															</button>
															<button
																type="button"
																className="btn btn-icon"
																title="Delete"
																data-toggle="tooltip"
																data-placement="top"
															>
																<i className="icon-trash text-danger" />
															</button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
										<nav aria-label="Page navigation">
											<ul className="pagination mb-0 justify-content-end">
												<li className="page-item">
													<a className="page-link" href="/#">
														Previous
													</a>
												</li>
												<li className="page-item active">
													<a className="page-link" href="/#">
														1
													</a>
												</li>
												<li className="page-item">
													<a className="page-link" href="/#">
														2
													</a>
												</li>
												<li className="page-item">
													<a className="page-link" href="/#">
														3
													</a>
												</li>
												<li className="page-item">
													<a className="page-link" href="/#">
														Next
													</a>
												</li>
											</ul>
										</nav>
									</div>
								</div>
							</div>
							<div className="tab-pane fade" id="Payroll-Payslip" role="tabpanel">
								<div className="card">
									<div className="card-body">
										<div className="media mb-4">
											<div className="mr-3">
												<img
													className="rounded"
													src="../assets/images/xs/avatar4.jpg"
													alt="fake_url"
												/>
											</div>
											<div className="media-body">
												<div className="content">
													<span>
														<strong>Order ID: </strong> C09
													</span>
													<p className="h5">
														John Smith{' '}
														<small className="float-right badge badge-primary">
															Jun 15, 2019
														</small>
													</p>
													<p>795 Folsom Ave, Suite 546 San Francisco, CA 54656</p>
												</div>
												<nav className="d-flex text-muted">
													<a href="fake_url" className="icon mr-3">
														<i className="icon-envelope text-info" />
													</a>
													<a href="fake_url" className="icon mr-3">
														<i className="icon-printer" />
													</a>
												</nav>
											</div>
										</div>
										<div className="table-responsive">
											<table className="table table-hover table-striped table-vcenter">
												<thead className="dark-mode">
													<tr>
														<th className="w60">#</th>
														<th />
														<th className="w100">Earnings</th>
														<th className="w100">Deductions</th>
														<th className="w100 text-right">Total</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>01</td>
														<td>
															<span>Basic Salary</span>
														</td>
														<td>$1,500</td>
														<td>-</td>
														<td className="text-right">$380</td>
													</tr>
													<tr>
														<td>02</td>
														<td>
															<span>House Rent Allowance (H.R.A.)</span>
														</td>
														<td>$62</td>
														<td>-</td>
														<td className="text-right">$250</td>
													</tr>
													<tr>
														<td>03</td>
														<td>
															<span>Tax Deducted at Source (T.D.S.)</span>
														</td>
														<td>-</td>
														<td>$80</td>
														<td className="text-right">$120</td>
													</tr>
													<tr>
														<td>04</td>
														<td>
															<span>C/Bank Loan</span>
														</td>
														<td>-</td>
														<td>$120</td>
														<td className="text-right">$120</td>
													</tr>
													<tr>
														<td>05</td>
														<td>
															<span>Other Allowance</span>
														</td>
														<td>$121</td>
														<td>-</td>
														<td className="text-right">$120</td>
													</tr>
												</tbody>
												<tfoot>
													<tr>
														<td colSpan={2}>
															<span>
																<strong>Note:</strong> Ipsum is simply dummy text of the
																printing and typesetting industry.
															</span>
														</td>
														<td>$1683</td>
														<td>$200</td>
														<td className="text-right">
															<strong className="text-success">$1483.00</strong>
														</td>
													</tr>
												</tfoot>
											</table>
											<button className="btn btn-info float-right">
												<i className="icon-printer" /> Print
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</>
		);
	}
}
const mapStateToProps = state => ({
	fixNavbar: state.settings.isFixNavbar
})

const mapDispatchToProps = dispatch => ({})
export default connect(mapStateToProps, mapDispatchToProps)(Payroll);