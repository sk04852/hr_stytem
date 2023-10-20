import React from "react";
import ReactDOM from "react-dom/client";
import {Provider} from "react-redux";
import {ConfigureStore} from "./store";
import "./index.css";
import App from "./App";
import * as serviceWorker from "./serviceWorker";
// import registerServiceWorker from './registerServiceWorker';
import {PersistGate} from "redux-persist/integration/react";
import {Loader} from "./components/constants/loaders";
import axios from "axios";
import {autoLogoutUser} from "./redux/actions/usersAction";
import {GoogleOAuthProvider} from '@react-oauth/google';

import './components/Shared/i18n/i18n';

const clientId = "559550829293-hh6v7c2resega70n1tcbse38md1tmt5n.apps.googleusercontent.com"; //Vladlena

const {persistor, store} = ConfigureStore();

const UNAUTHORIZED = 401;
const {dispatch} = store;
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const {status} = error.response;
        if (status === UNAUTHORIZED) {
            dispatch(autoLogoutUser());
        }
        return Promise.reject(error);
    }
);

const root = ReactDOM.createRoot(document.getElementById("root"));

root.render(
    <Provider store={store}>
        <PersistGate loading={<Loader/>} persistor={persistor}>
            <GoogleOAuthProvider clientId={clientId}>
                <App/>
            </GoogleOAuthProvider>
        </PersistGate>
    </Provider>
);
// registerServiceWorker();
// ReactDOM.render(<App />, document.getElementById('root'));

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();
