import React, { useEffect ,useState} from "react";
import { Link, useHistory } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { loginUser } from "../../redux/actions/usersAction";
import { Formik, Field, Form } from "formik";
import Toast from "../constants/toast";

const Login = () => {
  const history = useHistory();
  const dispatch = useDispatch();
  const userSelector = useSelector((state) => state.users);
  const [email, setemail]=useState('')
  useEffect(() => {
    if (userSelector.isAuthenticated) {
      // localStorage.setItem("name",email);
      history.push("/dashboard");
    }
  }, []);

  const initinalValues = {
    email: "",
    password: "",
  };

  const handleSubmit = (values) => {
    // setemail(values.email)
    dispatch(loginUser(values, history));
  };

  return (
    <>
      <div className="auth">
        <div className="auth_left">
          <div className="card">
            <div className="text-center mb-2">
              <Link className="header-brand" to="/">
                <i className="fe fe-command brand-logo" />
              </Link>
            </div>
            <div className="card-body">
              <div className="card-title">Logi sisse</div>
              <Formik
                initialValues={initinalValues}
                onSubmit={(values) => handleSubmit(values)}
              >
                <Form>
                  <fieldset disabled={userSelector.isLoading}>
                    {/*<div className="form-group">*/}
                    {/*  <select className="custom-select">*/}
                    {/*    <option>HR Dashboard</option>*/}
                    {/*    /!*<option>Project Dashboard</option>*!/*/}
                    {/*    /!*<option>Job Portal</option>*!/*/}
                    {/*  </select>*/}
                    {/*</div>*/}
                    <div className="form-group">
                      <Field
                        type="email"
                        className="form-control"
                        id="email"
                        name="email"
                        aria-describedby="emailHelp"
                        placeholder="Sisesta email"
                      />
                    </div>
                    <div className="form-group">
                      <label className="form-label">
                        Parool
                        <Link
                          className="float-right small"
                          to="/forgotpassword"
                        >
                          Unustasin parooli
                        </Link>
                      </label>
                      <Field
                        type="password"
                        className="form-control"
                        id="password"
                        name="password"
                        placeholder="Parool"
                      />
                    </div>
                    {/*<div className="form-group">*/}
                    {/*  <label className="custom-control custom-checkbox">*/}
                    {/*    <input*/}
                    {/*      type="checkbox"*/}
                    {/*      className="custom-control-input"*/}
                    {/*    />*/}
                    {/*    <span className="custom-control-label">*/}
                    {/*      Remember me*/}
                    {/*    </span>*/}
                    {/*  </label>*/}
                    {/*</div>*/}
                    <div className="form-footer">
                      <button
                        className="btn btn-primary btn-block"
                        type={"submit"}
                        disabled={userSelector.isLoading}
                      >
                        {userSelector.isLoading
                          ? "Login sisse"
                          : "Vajuta, et sisse logida"}
                      </button>
                    </div>
                  </fieldset>
                </Form>
              </Formik>
            </div>
            <div className="text-center text-muted">
              {/* Don't have account yet? <Link to="/signup">Sign Up</Link> */}
            </div>
          </div>
        </div>
        <div className="auth_right">
          <div
            className="carousel slide"
            data-ride="carousel"
            data-interval={3000}
          >
            <div className="carousel-inner">
              <div className="carousel-item active">
                <img
                  src="assets/images/slider1.svg"
                  className="img-fluid"
                  alt="login page"
                />
                <div className="px-4 mt-4">
                  <h4>MSHR</h4>
                </div>
              </div>
              <div className="carousel-item">
                <img
                  src="assets/images/slider2.svg"
                  className="img-fluid"
                  alt="login page"
                />
                <div className="px-4 mt-4">
                  <h4>MSHR</h4>
                  
                </div>
              </div>
              <div className="carousel-item">
                <img
                  src="assets/images/slider3.svg"
                  className="img-fluid"
                  alt="login page"
                />
                <div className="px-4 mt-4">
                  <h4>MSHR</h4>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Toast />
    </>
  );
};

export default Login;
