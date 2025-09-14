const Unauthorized = () => {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <div className="text-center">
        <h2 className="text-2xl font-bold text-red-600">Unauthorized</h2>
        <p className="mt-2">You do not have permission to access this page.</p>
      </div>
    </div>
  );
};

export default Unauthorized;