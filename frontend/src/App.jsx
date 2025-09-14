import { BrowserRouter } from 'react-router-dom';
import { Suspense } from 'react';
import Routes from './routes';
import { AuthProvider } from './hooks/useAuth';
import LoadingSpinner from './components/common/LoadingSpinner';

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Suspense fallback={<LoadingSpinner />}>
          <Routes />
        </Suspense>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;