import React from 'react';

type CardProps = {
  children: React.ReactNode;
  className?: string;
};

export function Card({ children, className }: CardProps) {
  const defaultClasses = "rounded-2xl shadow-md p-4 bg-white border border-gray-200";
  return (
    <div className={`${defaultClasses} ${className || ""}`}>
      {children}
    </div>
  );
}

export function CardContent({ children }: { children: React.ReactNode }) {
  return <div className="mt-2">{children}</div>;
}

