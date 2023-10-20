import { createStore, applyMiddleware, compose } from "redux";
import { persistStore, persistReducer } from "redux-persist";
import { encryptTransform } from "redux-persist-transform-encrypt";
import storage from "redux-persist/lib/storage";
import thunk from "redux-thunk";
import rootReducer from "./redux/reducers/rootReducer";

const encryptor = encryptTransform({
  secretKey: "this-is-my-secret-key-2023",
});

const persistConfig = {
  key: "root",
  storage: storage,
  whitelist: ["users"],
  transforms: [encryptor],
};

const persistedReducer = persistReducer(persistConfig, rootReducer);
const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

export const ConfigureStore = () => {
  let store = createStore(
    persistedReducer,
    composeEnhancers(applyMiddleware(thunk))
  );
  let persistor = persistStore(store);
  return { store, persistor };

  // const composeEnhancers =
  //   window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;
  // return createStore(rootReducer, composeEnhancers(applyMiddleware(thunk)));
};
